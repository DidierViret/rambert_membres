<?php

namespace Members\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use Access\Exceptions\AccessDeniedException;
use Access\Models\AccessModel;
use Members\Models\PersonModel;
use Members\Models\HomeModel;
use Members\Models\CategoryModel;
use Members\Models\ContributionModel;
use Members\Models\NewsletterModel;
use Members\Models\NewsletterSubscriptionModel;

class MembersAdmin extends BaseController
{
    /**
     * Constructor
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {   
        // Set Access level before calling parent constructor
        // Manager and Admin access only
        $this->accessLevel = config('\Access\Config\AccessConfig')->access_lvl_manager;
        parent::initController($request, $response, $logger);

        // Load required helpers

        // Load required models
        $this->personModel = new PersonModel();
        $this->accessModel = new AccessModel();
        $this->homeModel = new HomeModel();
        $this->categoryModel = new CategoryModel();
        $this->contributionModel = new ContributionModel();
        $this->newsletterModel = new NewsletterModel();
        $this->newsletterSubscriptionModel = new NewsletterSubscriptionModel();
    }

    /**
     * Display a form to update a home
     */
    public function homeUpdate($id)
    {
        $data['home'] = $this->homeModel->find($id);
        $data['persons'] = $this->personModel->where('fk_home', $id)->findAll();

        foreach($data['persons'] as &$person) {
             // Access levels informations
             $accesses = $this->accessModel->where('fk_person', $person['id'])->findAll();
             foreach($accesses as $access) {
                 $person['access_levels'][] = $access['access_level'];
             }

            // Current contributions informations
            $person['contributions'] = $this->contributionModel->getOrdered($person['id'], true, 'date_begin', 'DESC');

            foreach($person['contributions'] as &$contribution) {
                // Only keep the year of the dates
                $contribution['date_begin'] = date('Y', strtotime($contribution['date_begin']));
                if(!empty($contribution['date_end'])) {
                    $contribution['date_end'] = date('Y', strtotime($contribution['date_end']));
                }
            }

            // Newsletter subscriptions informations
            $person['newsletter_subscriptions'] = $this->newsletterSubscriptionModel->where('fk_person', $person['id'])->findAll();
        }

        return $this->display_view('Members\home_form', $data);
    }

    /**
     * Create or update a home
     */
    public function homeSave($id = 0) {
        // Check if the user has the right to access this page
        if($this->session->get('access_level') < $this->accessLevel) {
            throw AccessDeniedException::forPageAccessDenied();
        }

        // Get the home informations
        if($id > 0) {
            $home['id'] = $id;
        } else {
            $home[] = [];
        }
        $home = array_merge($home, $this->request->getPost());

        if($id == 0) {
            // Create the home
            $id = $this->homeModel->insert($home);
        } else {
            // Update the home
            $this->homeModel->update($id, $home);
        }

        // Redirect to the home details page
        return redirect()->to('/home/'.$id);
    }
}

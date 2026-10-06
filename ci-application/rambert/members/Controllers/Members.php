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

class Members extends BaseController
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

    public function index()
    {
        return redirect()->to('/members');
    }

    /**
     * Display the list of all members
     */
    public function membersList()
    {
        // Get the persons to display
        $text_filter = $this->request->getGet('tf');

        // Member status filter : active (default), deleted or all
        $member_status = $this->request->getGet('ms');
        $withDeleted = ($member_status == 'all');
        $onlyDeleted = ($member_status == 'deleted');

        if(!empty($text_filter)) {
            $data['persons'] = $this->personModel->getByText($text_filter, $withDeleted, $onlyDeleted);
        } else {
            $data['persons'] = $this->personModel->getOrdered('last_name', 'ASC', $withDeleted, $onlyDeleted);
        }

        // Append all needed informations for the list to display
        if(!empty($data['persons'])) {
            foreach($data['persons'] as &$person) {
                // Access levels informations
                $accesses = $this->accessModel->where('fk_person', $person['id'])->findAll();
                foreach($accesses as $access) {
                    $person['access_levels'][] = $access['access_level'];
                }

                // Category informations (include soft deleted categories for old members)
                $person['category'] = $this->categoryModel->withDeleted()->find($person['fk_category']);

                // Home informations (include soft deleted homes for old members)
                $person['home'] = $this->homeModel->withDeleted()->find($person['fk_home']);

                // Other home members informations
                $homeMembers = $this->personModel->where('fk_home', $person['fk_home'])->findAll();
                foreach($homeMembers as $homeMember) {
                    if($homeMember['id'] != $person['id']) {
                        $person['other_home_members'][] = $homeMember;
                    }
                }

                // Current contributions informations
                $contributions = $this->contributionModel->where(['fk_person' => $person['id'], 'date_end' => null])->findAll();
                foreach($contributions as $contribution) {
                    $person['roles'][] = $contribution['role'];
                }
            }
        }

        return $this->display_view('Members\members_list', $data);
    }

    /**
     * Display the details of a home
     */
    public function homeDetails($id)
    {
        // Member status filter : active (default) or all
        $member_status = $this->request->getGet('ms');

        // Include soft deleted homes so that archived homes can still be displayed
        $data['home'] = $this->homeModel->withDeleted()->find($id);
        $data['persons'] = $this->personModel->withDeleted($member_status == 'all')->where('fk_home', $id)->findAll();

        // If no filter is given and all persons of the home are archived, display all persons by default
        if(empty($member_status) && empty($data['persons'])) {
            $data['persons'] = $this->personModel->withDeleted()->where('fk_home', $id)->findAll();
            if(!empty($data['persons'])) {
                // If there are still no persons, it means the home is empty and we can keep the default filter (active)
                $member_status = 'all';
            }
        }
        $data['member_status'] = ($member_status == 'all') ? 'all' : 'active';

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
            $person['newsletters'] = $this->newsletterModel->findAll();
            foreach($person['newsletters'] as &$newsletter) {
                // Check if the person is subscribed to the newsletter
                $subscription = $this->newsletterSubscriptionModel->where(['fk_person' => $person['id'], 'fk_newsletter' => $newsletter['id']])->findAll();
                if(!empty($subscription)) {
                    $newsletter['subscribed'] = true;
                } else {
                    $newsletter['subscribed'] = false;
                }
            }
        }

        return $this->display_view('Members\home_details', $data);
    }
}

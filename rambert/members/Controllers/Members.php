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

class Members extends BaseController
{
    /**
     * Constructor
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {   
        // Set Access level before calling parent constructor
        // Public access
        $this->accessLevel = config('\Access\Config\AccessConfig')->access_lvl_manager;
        parent::initController($request, $response, $logger);

        // Load required helpers

        // Load required models
        $this->personModel = new PersonModel();
        $this->accessModel = new AccessModel();
        $this->homeModel = new HomeModel();
        $this->categoryModel = new CategoryModel();
        $this->contributionModel = new ContributionModel();
    }

    public function index()
    {
        return $this->membersList();
    }

    public function membersList()
    {
        // Get the persons to display
        $data['persons'] = $this->personModel->getOrdered(false, 'last_name', 'ASC');

        // Append all needed informations for the list to display
        foreach($data['persons'] as &$person) {
            // Access levels informations
            $accesses = $this->accessModel->where('fk_person', $person['id'])->findAll();
            foreach($accesses as $access) {
                $person['access_levels'][] = $access['access_level'];
            }

            // Category informations
            $person['category'] = $this->categoryModel->find($person['fk_category']);

            // Home informations
            $person['home'] = $this->homeModel->find($person['fk_home']);

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

        return $this->display_view('Members\members_list', $data);
    }
}

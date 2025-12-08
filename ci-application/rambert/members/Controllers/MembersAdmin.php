<?php

namespace Members\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use Access\Exceptions\AccessDeniedException;
use Access\Models\AccessModel;
use Access\Models\AccessLevelModel;
use Members\Models\PersonModel;
use Members\Models\HomeModel;
use Members\Models\CategoryModel;
use Members\Models\ContributionModel;
use Members\Models\NewsletterModel;
use Members\Models\NewsletterSubscriptionModel;
use Members\Models\ChangeModel;
use Members\Models\ChangeTypeModel;
use Members\Models\TeamModel;
use Members\Models\RoleModel;

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
        $this->accessLevelModel = new AccessLevelModel();
        $this->homeModel = new HomeModel();
        $this->categoryModel = new CategoryModel();
        $this->contributionModel = new ContributionModel();
        $this->newsletterModel = new NewsletterModel();
        $this->newsletterSubscriptionModel = new NewsletterSubscriptionModel();
        $this->changeModel = new ChangeModel();
        $this->changeTypeModel = new ChangeTypeModel();
        $this->teamModel = new TeamModel();
        $this->roleModel = new RoleModel();
    }

    /**
     * Display a form to update a home
     */
    public function homeUpdate($id)
    {
        $data['home'] = $this->homeModel->find($id);
        $data['persons'] = $this->personModel->where('fk_home', $id)->findAll();

        foreach($data['persons'] as &$person) {
            $this->get_person_informations($person);
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
            $home_old = $this->homeModel->find($id);
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

    /**
     * Display a form to update a person
     */
    public function personUpdate($id) {
        $homeId = $this->personModel->find($id)['fk_home'];
        $data['home'] = $this->homeModel->find($homeId);
        $data['persons'] = $this->personModel->where('fk_home', $homeId)->findAll();
        $data['person_to_update'] = $id;
        $data['access_levels'] = $this->accessLevelModel->find();
        $data['categories'] = $this->categoryModel->findAll();

        foreach($data['persons'] as &$person) {
            $this->get_person_informations($person);
        }

        return $this->display_view('Members\home_person_form', $data);
    }

    /**
     * Create or update a person
     */
    public function personSave($id = 0) {
        // Check if the user has the right to access this page
        if($this->session->get('access_level') < $this->accessLevel) {
            throw AccessDeniedException::forPageAccessDenied();
        }

        // Get the person informations
        if($id > 0) {
            // Person allready exists, update her informations
            $person = $this->request->getPost();
            $personOld = $this->personModel->find($id);

        } else {
            $person[] = [];
        }


        
        if($id == 0) {
            // Create the person
            //$id = $this->personModel->insert($person);
        } else {
            // Update the person's access levels
            if($this->session->get('access_level') >= config('\Access\Config\AccessConfig')->access_lvl_admin) {
                $this->updatePersonAccesslevel($id, $person['access_level']);
            }
            unset($person['access_level']);

            // Update the person's newsletter subscriptions
            if(empty($person['newsletters'])) {
                $this->newsletterSubscriptionModel->where('fk_person', $id)->delete();
            } else {
                // Get the current subscriptions
                $subscriptions = $this->newsletterSubscriptionModel->where('fk_person', $id)->findAll();
                foreach($subscriptions as $subscription) {
                    // Check if the subscription is still valid
                    if(!in_array($subscription['fk_newsletter'], $person['newsletters'])) {
                        // Delete the subscription
                        $this->newsletterSubscriptionModel->delete($subscription['id']);
                    }
                }

                // Add the new subscriptions
                foreach($person['newsletters'] as $newsletter) {
                    // Check if the subscription already exists
                    $subscription = $this->newsletterSubscriptionModel->where(['fk_person' => $id, 'fk_newsletter' => $newsletter])->findAll();
                    if(empty($subscription)) {
                        // Add the subscription
                        $data = [
                            'fk_person' => $id,
                            'fk_newsletter' => $newsletter,
                        ];
                        $this->newsletterSubscriptionModel->insert($data);
                    }
                }
            }

            // Update the person
            $this->personModel->update($id, $person);
        }

        // Redirect to the person's home details page
        return redirect()->to('/home/'.$person['fk_home']);
    }

    /**
     * Get the informations linked to a person
     */
    private function get_person_informations(&$person) {
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

    /**
     * Update a person's access level
     */
    private function updatePersonAccesslevel($personId, $accessLevelId) {
        // Check if the user has admin rights
        if($this->session->get('access_level') >= config('\Access\Config\AccessConfig')->access_lvl_admin) {
            $personOld['accesses'] = $this->accessModel->where('fk_person', $personId)->findAll();

            if(empty($personOld['accesses']) && !empty($accessLevelId)) {
                // Add the new person's access level
                $data = [
                    'fk_person' => $personId,
                    'fk_access_level' => $accessLevelId,
                    'password' => "Rambert1901",
                    'password_confirm' => "Rambert1901",
                ];
                $this->accessModel->insert($data);

            } elseif(!empty($accessLevelId)) {
                // Update the person's access level
                if($personOld['accesses'][0]['fk_access_level'] != $accessLevelId) {
                    $data = [
                        'fk_access_level' => $accessLevelId,
                    ];
                    $this->accessModel->update($personOld['accesses'][0]['id'], $data);
                }

            } else {
                // Delete the person's access level
                foreach($personOld['accesses'] as $access) {
                    $this->accessModel->delete($access['id']);
                }
            }
        }
    }

    /**
     * Display the list of a person's contributions to let the user update them
     * @param int $personId The person's ID
     */
    public function contributionsList($personId) {
        // Check if the user has the right to access this page
        if($this->session->get('access_level') < $this->accessLevel) {
            throw AccessDeniedException::forPageAccessDenied();
        }
        // Get the person's informations
        $person = $this->personModel->find($personId);

        $data['list_title'] = $person['last_name']." ".$person['first_name']." - ".lang('members_lang.subtitle_contributions_list');

        // Get the list of contributions
        $data['items'] = $this->contributionModel->getOrdered($personId, true, 'date_begin', 'DESC');
        
        // Prepare the contributions list
        foreach($data['items'] as &$contribution) {
            // Only keep the year of the dates
            $contribution['date_begin'] = date('Y', strtotime($contribution['date_begin']));
            if(!empty($contribution['date_end'])) {
                $contribution['date_end'] = date('Y', strtotime($contribution['date_end']));
            }
            // Get the team and role names as description of the contribution
            if(!empty($contribution['role'])) {
                $contribution['description'] = $contribution['role']['name'];

                if(!empty($contribution['role']['team'])) {
                    $contribution['description'] = $contribution['role']['team']['name']." : ".$contribution['role']['name'];
                }
            }
        }
        
        $data['columns'] = ['description' => 'Rôle',
                            'date_begin' => 'Début',
                            'date_end' => 'Fin'];
      
        $data['primary_key_field']  = 'id';
        $data['btn_create_label']   = lang('members_lang.btn_add');
        $data['url_update'] = "contribution/update/";
        $data['url_delete'] = "contribution/delete/";
        $data['url_create'] = "contribution/create/".$person['id'];

        return $this->display_view('Common\items_list', $data);
    }

    /**
     * Display a form to create a contribution
     */
    public function contributionCreate($personId) {
        // Check if the user has the right to access this page
        if($this->session->get('access_level') < $this->accessLevel) {
            throw AccessDeniedException::forPageAccessDenied();
        }

        // Get the person's informations
        $contribution['person'] = $this->personModel->find($personId);
        if(empty($contribution['person'])) {
            // Person not found, redirect to members list
            return redirect()->to(base_url('/members'));
        }
        $data['contribution'] = $contribution;

        // Form title
        $data['title'] = $contribution['person']['last_name']." ".$contribution['person']['first_name']." - ".lang('members_lang.subtitle_contribution_create');

        // Default values for a new contribution
        $data['contribution']['id'] = 0;
        $data['contribution']['role'] = ['id' => 0, 'team' => ['id' => 0]];
        $data['contribution']['date_begin'] = date('Y');
        $data['contribution']['date_end'] = '';

        // Get the list of teams
        $data['teams'] = $this->teamModel->getDropdown();
        // Get the list of roles
        $data['roles'] = $this->roleModel->findAll();

        return $this->display_view('Members\contribution_form', $data);
    }

    /**
     * Display a form to update a contribution
     */
    public function contributionUpdate($id) {
        // Check if the user has the right to access this page
        if($this->session->get('access_level') < $this->accessLevel) {
            throw AccessDeniedException::forPageAccessDenied();
        }

        // Get the contribution informations
        $contribution = $this->contributionModel->find($id);
        $data['contribution'] = $contribution;

        // Form title
        $data['title'] = $contribution['person']['last_name']." ".$contribution['person']['first_name']." - ".lang('members_lang.subtitle_contribution_update');

        // Keep the year of the dates
        $data['contribution']['date_begin'] = date('Y', strtotime($data['contribution']['date_begin']));
        if(!empty($data['contribution']['date_end'])) {
            $data['contribution']['date_end'] = date('Y', strtotime($data['contribution']['date_end']));
        }

        // Get the list of teams
        $data['teams'] = $this->teamModel->getDropdown();
        // Get the list of roles
        $data['roles'] = $this->roleModel->findAll();

        return $this->display_view('Members\contribution_form', $data);
    }

    /**
     * Create or update a contribution
     */
    public function contributionSave($id = 0) {
        // Check if the user has the right to access this page
        if($this->session->get('access_level') < $this->accessLevel) {
            throw AccessDeniedException::forPageAccessDenied();
        }

        // Get the contribution informations
        $contribution = $this->request->getPost();

        // Convert year-only dates to MySQL format (YYYY-MM-DD)
        $contribution['date_begin'] = $contribution['date_begin'] . '-01-01';
        if(!empty($contribution['date_end'])) {
            $contribution['date_end'] = $contribution['date_end'] . '-12-31';
        } else {
            $contribution['date_end'] = null;
        }

        if($id == 0) {
            // Create the contribution
            $id = $this->contributionModel->insert($contribution);
        } else {
            // Update the contribution
            $this->contributionModel->update($id, $contribution);
        }

        // Redirect to the contributions list page
        return redirect()->to(base_url('/contributions/'.$contribution['fk_person']));
    }

    /**
     * Display a confirmation message for the deletion of a contribution
     */
    public function contributionConfirmDelete($id) {
        // Check if the user has the right to access this page
        if($this->session->get('access_level') < $this->accessLevel) {
            throw AccessDeniedException::forPageAccessDenied();
        }

        // Get the contribution informations
        $contribution = $this->contributionModel->find($id);
        $person = $this->personModel->find($contribution['fk_person']);
        $contributionTeamName = (!empty($contribution['role']['team'])) ? $contribution['role']['team']['name'] : '';

        // Confirmation message
        $data['message'] = lang('members_lang.msg_contribution_confirm_delete')."<br><strong>".$person['first_name'].' '.$person['last_name'].' : '.$contributionTeamName.' - '.$contribution['role']['name']."</strong>";
        $data['url_yes'] = base_url('/contribution/delete/'.$id);
        $data['url_no'] = base_url('/contributions/'.$person['id']);

        return $this->display_view('Common\confirm_delete', $data);
    }

    /**
     * Delete a contribution
     */
    public function contributionDelete($id) {
        // Check if the user has the right to access this page
        if($this->session->get('access_level') < $this->accessLevel) {
            throw AccessDeniedException::forPageAccessDenied();
        }

        // Get the contribution informations
        $contribution = $this->contributionModel->find($id);
        $personId = $contribution['fk_person'];

        // Delete the contribution
        $this->contributionModel->delete($id);

        // Redirect to the contributions list page
        return redirect()->to(base_url('/contributions/'.$personId));
    }
}

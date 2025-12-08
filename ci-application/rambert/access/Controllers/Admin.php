<?php
/**
 * Manage access rights, for administrators only
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */

namespace Access\Controllers;

use App\Controllers\BaseController;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Response;
use Psr\Log\LoggerInterface;

use Access\Models\AccessModel;
use Access\Models\AccessLevelModel;
use Members\Models\PersonModel;

class Admin extends BaseController
{
    /**
     * Constructor
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        // Set Access level before calling parent constructor
        // Administrators access only
        $this->accessLevel = config('Access\AccessConfig')->access_lvl_admin;
        parent::initController($request, $response, $logger);

        // Load required helpers
        helper('form');

        // Load required models
        $this->accessModel = new AccessModel();
        $this->accessLevelModel = new AccessLevelModel();
        $this->personModel = new PersonModel();
    }

    public function test() {
        dd($this->accessModel->getlist());
    }

    /**
     * Display the list of existing access rights
     * 
     * @return string : The view containing the list of existing access rights
     */
    public function listAccess($with_deleted = false): string {

        $data['list_title'] = lang('access_lang.title_access_list');
        
        $data['columns'] = ['last_name' => lang('access_lang.col_last_name'),
                            'first_name' => lang('access_lang.col_first_name'),
                            'access_level_name' => lang('access_lang.col_access_level'),
                            'email' => lang('access_lang.col_email'),
        ];
      
        $data['items'] = $this->accessModel->getList($with_deleted);
     
        $data['primary_key_field']  = 'id';
        $data['btn_create_label']   = lang('common_lang.btn_new_m');
        $data['with_deleted']       = $with_deleted;
        $data['deleted_field']      = 'date_delete';
        $data['url_update']         = 'access/update/';
        $data['url_delete']         = 'access/delete/';
        $data['url_create']         = 'access/create';
        $data['url_getView']        = 'access/list';
        $data['url_restore']        = 'access/restore/';
     
	    return $this->display_view('Common\Views\items_list', $data);
    }

    /**
     * Display a form to add new access rights
     * 
     * @return string : The view containing the form
     */
    public function createAccess(): string {
        $access_levels = $this->accessLevelModel->getDropdown();
        $data['access_levels'] = $access_levels;
    
	    return $this->display_view('Access\Views\access_form', $data);
    }

    /**
     * Display a form to update existing access rights
     * 
     * @return string : The view containing the form
     */
    public function updateAccess(int $id = 0): string|response {
        $data['access_levels'] = $this->accessLevelModel->getDropdown();

        if ($id == 0) {
            return redirect()->to('access');
        } else {
            $data['access'] = $this->accessModel->find($id);
        }
    
	    return $this->display_view('Access\Views\access_form', $data);
    }

    /**
     * Method called from the access_form to create new access rights or update existing access rights
     *
     * @return : The newly created or modified access object
     */
    public function saveAccess(): string|Response
    {
        // Get posted datas and find the person corresponding to the given e-mail address
        if (count($_POST) > 0) {
            $accessId = $this->request->getPost('id');

            $access = [];
            if ($accessId > 0) {
                // An ID is defined, the existing access will be updated
                $access = $this->accessModel->find($accessId);
                // Don't keep hashed password in $access array
                unset($access['password']);
            }

            $access['fk_access_level'] = $this->request->getPost('access_level');
            
            // Password could be empty in case of an update
            if (!empty($this->request->getPost('password'))) {
                $access['password'] = $this->request->getPost('password');
                $access['password_confirm'] = $this->request->getPost('password_confirm');
            }

            // Get the person corresponding to the given e-mail
            $email = $this->request->getPost('email');
            $person = $this->personModel->where('email', $email)->first();

            if (empty($person)) {
                // The given e-mail is not corresponding to an existing person, display error in the form
                $data['errors'] = ['email' => lang('access_lang.msg_error_email_not_matches')];
                // Send the given e-mail to the form, to let it display it again
                $access['person']['email'] = $email;

            } else {
                // Give access to the matching person
                $access['fk_person'] = $person['id'];
                $this->accessModel->save($access);

                // If no error occured, the operation is completed, redirect to access list
                if ($this->accessModel->errors() == null) {
                    return redirect()->to('access');
                } else {
                    // They were validation errors, display them in the form
                    $data['errors'] = $this->accessModel->errors();
                }
            }

        } else {
            // No data posted, redirect to access list
            return redirect()->to('access');
        }
        
        // They were some errors, display the access form again
        $data['access_levels'] = $this->accessLevelModel->getDropdown();
        $data['access'] = $access;
        
        return $this->display_view('Access\Views\access_form', $data);
    }

    /**
     * Method called to delete access rights
     *
     * @return : The newly created or modified access object
     */
    public function deleteAccess(int $accessId, ?int $action = 0): string|Response {
        $access = $this->accessModel->withDeleted()->find($accessId);

        // No access rights corresponding to the given id
        if (empty($access)) {
            return redirect()->to('access');
        }

        switch($action) {
            case 0: // Display confirmation
                $data = array(
                    'access' => $access,
                    'title' => lang('access_lang.title_access_delete')
                );
                return $this->display_view('\User\admin\delete_user', $data);
                break;

            case 1: // Disable (soft delete) access rights
                if ($_SESSION['access_id'] != $accessId) {
                    $this->accessModel->delete($accessId, FALSE);
                }
                return redirect()->to('access');
                break;

            case 2: // Hard delete access rights
                if ($_SESSION['access_id'] != $accessId) {
                    $this->accessModel->delete($accessId, TRUE);
                }
                return redirect()->to('access');
                break;

            default: // Do nothing
                return redirect()->to('access');
        }
    }

    /**
     * Update an access' password with a newly defined password
     * @param int $accessId The access' ID
     */
    public function updatePassword($accessId) {
        $access = $this->accessModel->find($accessId);

        // No access rights corresponding to the given id
        if (empty($access)) {
            return redirect()->to('access');
        }

        $data['access'] = $access;
        $data['errors'] = [];

        // Check if the form has been submitted, else just display the form
        if (!is_null($this->request->getVar('btn_update_password'))) {
            $access['password'] = $this->request->getVar('new_password');
            $access['password_confirm'] = $this->request->getVar('password_confirm');

            $this->accessModel->update($access['id'], $access);

            if ($this->accessModel->errors()==null) {
                // No error happened, redirect
                return redirect()->to('access');
            } else {
                // Display error messages
                $data['errors'] = $this->accessModel->errors();
            }
        }

        // Display the password change form
        $data['title'] = lang('access_lang.title_access_password_reset').' : '.$access['person']['first_name'].' '.$access['person']['last_name'];
        return $this->display_view('\Access\update_password', $data);
    }
} ?>
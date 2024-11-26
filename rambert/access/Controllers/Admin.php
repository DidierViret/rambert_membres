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
        $access_levels = $this->accessLevelModel->getDropdown();
        $data['access_levels'] = $access_levels;

        if ($id == 0) {
            return redirect()->to('access');
        } else {
            $data['access'] = $this->accessModel->find($id);
        }
    
	    return $this->display_view('Access\Views\access_form', $data);
    }

    /**
     * Save new access rights or update existing access rights
     *
     * @param integer $id = The id of the access to modify, leave blank to create a new one
     * @return : The newly created or modified access object
     */
    public function saveAccess(?int $id = 0): string|Response
    {
        //added user in current scope to manage its datas
        $user=null;
        if (count($_POST) > 0) {
            $user_id = $this->request->getPost('id');
            $oldName = $this->request->getPost('user_name');
            if($_SESSION['user_id'] != $user_id) {
                $oldUsertype = $this->request->getPost('user_usertype');
            }
            $user = array(
                'id'    => $user_id,
                'fk_user_type' => intval($this->request->getPost('user_usertype')),
                'username' => $this->request->getPost('user_name'),
                'email' => $this->request->getPost('user_email') ?: NULL
            );
            if($this->request->getPost('user_password_again') !== null) {
                $user['password_confirm'] = $this->request->getPost('user_password_again');
            }
            if ($user_id > 0) {
                $this->user_model->update($user_id, $user);
            }
            else {
                $user['password'] = $this->request->getPost('user_password');
                $user['password_confirm'] = $this->request->getPost('user_password_again');

                $this->user_model->insert($user);
            }
            //In the case of errors
            if ($this->user_model->errors()==null){
                return redirect()->to('/user/admin/list_user');
            }
        }

        //usertiarray is an array contained all usertype name and id
        $usertiarray=$this->db->table('user_type')->select(['id','name'],)->get()->getResultArray();
        $usertypes=[];
        foreach ($usertiarray as $row){
            $usertypes[$row['id']]=$row['name'];
        }
        $output = array(
            'title'         => lang('user_lang.title_user_'.((bool)$user_id ? 'update' : 'new')),
            'user'          => $this->user_model->withDeleted()->find($user_id),
            'user_types'    => $usertypes,
            'user_name'     => $oldName,
            'user_usertype' => $oldUsertype,
            'email'         => $user['email']??null,
            'errors'        => $this->user_model->errors()==null?[]:$this->user_model->errors()
        );

        return $this->display_view('\User\admin\form_user', $output);
    }
    
} ?>
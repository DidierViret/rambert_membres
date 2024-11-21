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
    }

    public function test() {
        dd($this->accessModel->getlist());
    }

    /**
     * Display the list of existing access rights
     * 
     * @return string : The view containing the list of existing access rights
     */
    public function list($with_deleted = false): string {

        $data['list_title'] = lang('access_lang.title_access_list');
        
        $data['columns'] = ['last_name' => lang('access_lang.col_last_name'),
                            'first_name' => lang('access_lang.col_first_name'),
                            'email' => lang('access_lang.col_email'),
                            'access_level_name' => lang('access_lang.col_access_level'),
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

    
} ?>
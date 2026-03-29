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

class Lists extends BaseController
{
    /**
     * Constructor
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {   
        // Set Access level before calling parent constructor
        // Access to any authentified user
        $this->accessLevel = "@";
        parent::initController($request, $response, $logger);

        // Load required helpers

        // Load required models
        $this->personModel = new PersonModel();
        $this->accessModel = new AccessModel();
        $this->homeModel = new HomeModel();
    }

    public function index() {
        $listType = $this->request->getGet('list-type');

        // If no list type is provided, default to 'postal-send'
        if ($listType === null) {
            $listType = 'postal-send';
        }

        $data['list_type'] = $listType;

        switch($listType) {
            case 'postal-send':
                $data['data'] = $this->getDataPostalSend();
                break;
            case 'newsletter-addresses':
                $data['data'] = $this->getDataNewsletterAddresses();
                break;
            case 'no-email-address':
                $data['data'] = $this->getDataNoEmailAddress();
                break;
            case 'all-members':
                $data['data'] = $this->getDataAllMembers();
                break;
            case 'all-members-with-soft-deleted':
                $data['data'] = $this->getDataAllMembersWithSoftDeleted();
                break;
            default:
                // Handle unknown list types
                return $this->response->setStatusCode(404)->setBody('List type not supported');
        }

        return $this->display_view('Members\Views\export_lists', $data);
    }

    /**
     * Get all datas needed for postal sends
     * (can be used to send bulletins or other postal communications)
     */
    public function getDataPostalSend()
    {
        $homes = $this->homeModel->findAll();
        $data = [];
        
        if(!empty($homes)) {
            $data['columns'] = [lang('members_lang.field_address_title'),
                                lang('members_lang.field_address_name'),
                                lang('members_lang.field_address_line_1'),
                                lang('members_lang.field_address_line_2'),
                                lang('members_lang.field_postal_code'),
                                lang('members_lang.field_city'),
                                lang('members_lang.field_nb_bulletins')
                            ];

            foreach($homes as $home) {
                $data['rows'][] = [
                    $home['address_title'],
                    $home['address_name'],
                    $home['address_line_1'],
                    $home['address_line_2'],
                    $home['postal_code'],
                    $home['city'],
                    $home['nb_bulletins']
                ];
            }
        }

        return $data;
    }

    public function getDataNewsletterAddresses()
    {
        $data['columns'] = ['test1', 'test2'];
        $data['rows'] = [
            ['value5', 'value6'],
            ['value7', 'value8']
        ];

        return $data;
    }
}

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
use Members\Models\NewsletterSubscriptionModel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Lists extends BaseController
{
    // TODO replace this constant for use with a set of different newsletter
    // So far we only manage one type of newsletter
    private $fk_newsletter = 1;

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
        $this->categoryModel = new CategoryModel();
        $this->newsletterSubscriptionModel = new NewsletterSubscriptionModel();
    }

    /**
     * Display the list of members based on the specified list type
     */
    public function index() {
        $listType = $this->request->getGet('list-type');

        // If no list type is provided, default to 'postal-send'
        if ($listType === null) {
            $listType = 'postal-send';
        }

        $data['list_type'] = $listType;
        $data['data'] = $this->getData($listType);

        return $this->display_view('Members\Views\export_lists', $data);
    }

    /**
     * Export in Excel file the list of members based on the specified list type
     */
    public function exportExcel()
    {
        // Get data corresponding to the specified list type
        $listType = $this->request->getGet('list-type');
        $data = $this->getData($listType);

        // Prepare a spreadsheet
        $file_name = 'data.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add column headers in the spreadsheet's first line
        $currentColumn = 1;
        foreach($data['columns'] as $column) {
            $sheet->setCellValue([$currentColumn, 1], $column);
            $currentColumn++;
        }

        // Add datas in the spreadsheet
        $currentRow = 2;
        foreach($data['rows'] as $rowColumns) {
            $currentColumn = 1;
            foreach($rowColumns as $value) {
                $sheet->setCellValue([$currentColumn, $currentRow], $value);
                $currentColumn++;
            }
            $currentRow++;
        }

        // Write and save the xlsx document
        $writer = new Xlsx($spreadsheet);
		$writer->save($file_name);

		header("Content-Type: application/vnd.ms-excel");
		header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length:' . filesize($file_name));
		flush();

		readfile($file_name);

		exit;
    }

    /**
     * Get data required for a specific list type
     */
    private function getData($listType)
    {
        switch($listType) {
            case 'postal-send':
                $data = $this->getDataPostalSend();
                break;
            case 'newsletter-addresses':
                $data = $this->getDataNewsletterAddresses($this->fk_newsletter);
                break;
            case 'no-email-address':
                $data = $this->getDataNoEmailAddress();
                break;
            case 'all-members':
                $data = $this->getDataAllMembers();
                break;
            case 'all-members-with-soft-deleted':
                $data = $this->getDataAllMembersWithSoftDeleted();
                break;
            default:
                // Handle unknown list types
                return $this->response->setStatusCode(404)->setBody('List type not supported');
        }

        return $data;
    }

    /**
     * Get all datas needed for postal sends
     * (can be used to send bulletins or other postal communications)
     */
    private function getDataPostalSend()
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

    /**
     * Get needed data for a list of all persons who have subscribed to a newsletter
     * Persons who don't have a known e-mail address can contain a fake
     * e-mail address with "pas-de-courriel" text in it.
     * These are not listed.
     */
    private function getDataNewsletterAddresses(int $newsletterId)
    {
        $subscriptions = $this->newsletterSubscriptionModel->where('fk_newsletter', $newsletterId)->findAll();
        $data = [];
        
        if(!empty($subscriptions)) {
            $data['columns'] = [lang('members_lang.field_last_name'),
                                lang('members_lang.field_first_name'),
                                lang('members_lang.field_email')
                            ];

            foreach($subscriptions as $subscription) {
                $person = $subscription['person'];
                if (!empty($person['email']) && !str_contains($person['email'], 'pas-de-courriel')) {
                    $data['rows'][] = [
                        $person['last_name'],
                        $person['first_name'],
                        $person['email']
                    ];
                }
            }
        }
        return $data;
    }

    /**
     * Get needed data for a list of all persons who don't have a known e-mail address
     * Persons who don't have a known e-mail address can contain a fake
     * e-mail address with "pas-de-courriel" text in it.
     * These are listed as they don't have a known valid address.
     */
    private function getDataNoEmailAddress()
    {
        $persons = $this->personModel->getOrdered(false, "last_name", "ASC");
        $data = [];
        
        if(!empty($persons)) {
            $data['columns'] = [lang('members_lang.field_last_name'),
                                lang('members_lang.field_first_name'),
                                lang('members_lang.field_home_address')
                            ];

            foreach($persons as $person) {
                if (empty($person['email']) || str_contains($person['email'], 'pas-de-courriel')) {
                    $home = $this->homeModel->find($person['fk_home']);
                    $data['rows'][] = [
                        $person['last_name'],
                        $person['first_name'],
                        $home['address_name'].", ".$home['address_line_1']." ".$home['address_line_2'].", ".$home['postal_code']." ".$home['city']
                    ];
                }
            }
        }
        return $data;
    }

    private function getDataAllMembers()
    {
        $persons = $this->personModel->getOrdered(false, "last_name", "ASC");
        $data = [];
        
        if(!empty($persons)) {
            $data['columns'] = [lang('members_lang.field_title'),
                                lang('members_lang.field_last_name'),
                                lang('members_lang.field_first_name'),
                                lang('members_lang.field_address_line_1'),
                                lang('members_lang.field_address_line_2'),
                                lang('members_lang.field_postal_code'),
                                lang('members_lang.field_city'),
                                lang('members_lang.field_phone_1'),
                                lang('members_lang.field_phone_2'),
                                lang('members_lang.field_email'),
                                lang('members_lang.field_category'),
                                lang('members_lang.field_birth'),
                                lang('members_lang.field_membership_start'),
                                lang('members_lang.field_comments')
                            ];

            foreach($persons as $person) {
                $home = $this->homeModel->find($person['fk_home']);
                $category = $this->categoryModel->find($person['fk_category']);
                $data['rows'][] = [
                    $person['title'],
                    $person['last_name'],
                    $person['first_name'],
                    $home['address_line_1'],
                    $home['address_line_2'],
                    $home['postal_code'],
                    $home['city'],
                    $person['phone_1'],
                    $person['phone_2'],
                    $person['email'],
                    $category['name'],
                    $person['birth'],
                    $person['membership_start'],
                    $person['comments']
                ];
            }
        }

        return $data;
    }

    private function getDataAllMembersWithSoftDeleted()
    {
        $persons = $this->personModel->getOrdered(true, "last_name", "ASC");
        $data = [];
        
        if(!empty($persons)) {
            $data['columns'] = [lang('members_lang.field_title'),
                                lang('members_lang.field_last_name'),
                                lang('members_lang.field_first_name'),
                                lang('members_lang.field_address_line_1'),
                                lang('members_lang.field_address_line_2'),
                                lang('members_lang.field_postal_code'),
                                lang('members_lang.field_city'),
                                lang('members_lang.field_phone_1'),
                                lang('members_lang.field_phone_2'),
                                lang('members_lang.field_email'),
                                lang('members_lang.field_category'),
                                lang('members_lang.field_birth'),
                                lang('members_lang.field_membership_start'),
                                lang('members_lang.field_membership_end'),
                                lang('members_lang.field_membership_end_reason'),
                                lang('members_lang.field_comments')
                            ];

            foreach($persons as $person) {
                $home = $this->homeModel->withDeleted()->find($person['fk_home']);
                $category = $this->categoryModel->find($person['fk_category']);
                $data['rows'][] = [
                    $person['title'],
                    $person['last_name'],
                    $person['first_name'],
                    $home['address_line_1'],
                    $home['address_line_2'],
                    $home['postal_code'],
                    $home['city'],
                    $person['phone_1'],
                    $person['phone_2'],
                    $person['email'],
                    $category['name'],
                    $person['birth'],
                    $person['membership_start'],
                    $person['membership_end'],
                    $person['membership_end_reason'],
                    $person['comments']
                ];
            }
        }

        return $data;
    }
}

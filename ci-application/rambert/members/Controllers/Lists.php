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

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
                $data = $this->getDataNewsletterAddresses();
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

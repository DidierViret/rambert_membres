<?php
/**
 * Model used to manage person informations
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
namespace Members\Models;

use CodeIgniter\Model;
use Members\Models\Homemodel;
use Members\Models\Categorymodel;

class PersonModel extends Model {
    protected $table = 'person';
    protected $primaryKey = 'id';

    // Columns allowed for save operations
    protected $allowedFields = [
        'id',
        'fk_home',
        'fk_category',
        'title',
        'first_name',
        'last_name',
        'email',
        'phone_1',
        'phone_2',
        'birth',
        'profession',
        'godfathers',
        'membership_start',
        'membership_end',
        'membership_end_reason',
        'comments',
        'date_delete',
    ];

    // Defines the use of soft_delete feature
    protected $useSoftDeletes = true;
    protected $deletedField = 'date_delete';

    // Callbacks
    protected $afterFind = ['appendHome', 'appendCategory'];

    public function initialize()
    {
        $this->homeModel = new HomeModel();
        $this->categoryModel = new CategoryModel();

        // Validation rules
        $this->validationRules = [
            'email' => 'permit_empty|valid_email|max_length[150]',
            'phone_1' => 'permit_empty|max_length[50]',
            'phone_2' => 'permit_empty|max_length[50]',
            'birth' => 'permit_empty',
            'membership_start' => 'permit_empty',
            'membership_end' => 'permit_empty',
            'title' => 'permit_empty|max_length[100]',
            'first_name' => 'permit_empty|max_length[100]',
            'last_name' => 'permit_empty|max_length[100]',
            'profession' => 'permit_empty|max_length[100]',
            'godfathers' => 'permit_empty|max_length[255]',
            'membership_end_reason' => 'permit_empty|max_length[255]',
            'comments' => 'permit_empty',
        ];

        // Custom error messages for validation
        $this->validationMessages = [
            'email' => [
                'valid_email' => lang('members_lang.msg_error_valid_email'),
                'max_length' => lang('members_lang.msg_error_email_length'),
            ],
            'birth' => [
                'valid_date' => 'The birth date must be in YYYY-MM-DD format.',
            ],
        ];
    }

    // Returns members by home
    public function getByHome(int $homeId)
    {
        return $this->where('fk_home', $homeId)
                    ->findAll();
    }

    // Returns members by category
    public function getByCategory(int $categoryId)
    {
        return $this->where('fk_category', $categoryId)
                    ->findAll();
    }

    /**
     * Callback method to append datas from the linked home table
     */
    protected function appendHome(array $data) {

        if($data['singleton'] && !empty($data['data'])) {
            // Single item, add datas to it
            $data['data']['home'] = $this->homeModel->find($data['data']['fk_home']);

        } elseif (!empty($data['data'])) {
            // Multiple items, add datas to each of them
            foreach ($data['data'] as &$person) {
                $person['home'] = $this->homeModel->find($person['fk_home']);
            }
        }
        return $data;
    }

    /**
     * Callback method to append datas from the linked category table
     */
    protected function appendCategory(array $data) {

        if($data['singleton'] && !empty($data['data'])) {
            // Single item, add datas to it
            $data['data']['category'] = $this->categoryModel->find($data['data']['fk_category']);

        } elseif (!empty($data['data'])) {
            // Multiple items, add datas to each of them
            foreach ($data['data'] as &$person) {
                $person['category'] = $this->categoryModel->find($person['fk_category']);
            }
        }
        return $data;
    }
}
?>
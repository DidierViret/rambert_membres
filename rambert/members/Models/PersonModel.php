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
use Members\Models\HomeModel;
use Members\Models\CategoryModel;

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

    /**
     * Callback method to append datas from the linked home table
     */
    protected function appendHome(array $data) {
        $homeModel = new HomeModel();

        if($data['singleton'] && !empty($data['data'])) {
            // Single item, add datas to it
            if (!empty($data['data']['fk_home'])) {
                $data['data']['home'] = $homeModel->find($data['data']['fk_home']);
            }

        } elseif (!empty($data['data'])) {
            // Multiple items, add datas to each of them
            foreach ($data['data'] as &$person) {
                if(!empty($person['fk_home'])) {
                    $person['home'] = $homeModel->find($person['fk_home']);
                }
            }
        }
        return $data;
    }

    /**
     * Callback method to append datas from the linked category table
     */
    protected function appendCategory(array $data) {
        $categoryModel = new CategoryModel();

        if($data['singleton'] && !empty($data['data'])) {
            // Single item, add datas to it
            if (!empty($data['data']['fk_category'])) {
                $data['data']['category'] = $categoryModel->find($data['data']['fk_category']);
            }

        } elseif (!empty($data['data'])) {
            // Multiple items, add datas to each of them
            foreach ($data['data'] as &$person) {
                if(!empty($person['fk_category'])) {
                    $person['category'] = $categoryModel->find($person['fk_category']);
                }
            }
        }
        return $data;
    }

    /**
     * Get an array of persons ordered by a field.
     * 
     * @param bool $withDeleted : A boolean to include or not the soft deleted persons
     * @param string $orderBy : name of the field to use to sort the persons
     * @param string $direction : ASC, DESC or RANDOM, the direction of the sorting
     * 
     * @return : An array of persons with all attributes, ordered by the mentioned field
     */
    public function getOrdered(bool $withDeleted = false, string $orderBy = 'id', string $direction = 'ASC') {
        $builder = $this->builder();
        $builder->select('*');
        $builder->orderBy("$orderBy", "$direction");
        if(!$withDeleted) {
            $builder->where('date_delete IS NULL');
        }

        $query = $builder->get();
        return $query->getResult('array');
    }
}
?>
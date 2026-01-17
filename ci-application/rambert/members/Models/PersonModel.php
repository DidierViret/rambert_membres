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
    protected $afterInsert = ['logCreate'];
    protected $beforeUpdate = ['keepOldValues'];
    protected $afterUpdate = ['logUpdate'];
    protected $beforeDelete = ['keepOldValues'];
    protected $afterDelete = ['logDelete'];

    // Variables used in callbacks
    private $oldValues = [];

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

    /**
     * Callback method to store old values before update
     */
    protected function keepOldValues(array $data) {

        // Store old values
        foreach ($data['id'] as $id) {
            $this->oldValues[$id] = $this->find($id);
        }
        
        return $data;
    }

    /**
     * Callback method to log the creation of a new person
     */
    protected function logCreate(array $data) {
        // Do not log the changes if the importation flag is set
        if (isset($_SESSION['importation']) && $_SESSION['importation'] == true) {
            return $data;
        }

        $personModel = new PersonModel();
        $homeModel = new HomeModel();
        $changeTypeModel = new ChangeTypeModel();
        $changeModel = new ChangeModel();

        if ($data['id'] != 0) {
            $newPerson = $data['data'];
            $home = $homeModel->find($newPerson['fk_home']);

            $changeTypeId = $changeTypeModel->getChangeTypeId('membership_start');
            
            $changeData = [
                'fk_change_author' => session()->get('user_id'),
                'fk_person_concerned' => $data['id'],
                'fk_change_type' => $changeTypeId,
                'field' => lang('members_lang.field_membership_start'),
                'value_old' => '',
                'value_new' => $newPerson['last_name'].' '.$newPerson['first_name']."\n".
                               lang('members_lang.field_membership_start').': '.$newPerson['membership_start']."\n".
                               ($home ? $home['address_line_1']."\n".
                                        $home['address_line_2']."\n".
                                        $home['postal_code'].' '.$home['city']
                                      : ''),
            ];
            $changeModel->insert($changeData);
        }

        return $data;
    }

    /**
     * Callback method to log updates made to a person
     */
    protected function logUpdate(array $data) {
        // Do not log the changes if the importation flag is set
        if (isset($_SESSION['importation']) && $_SESSION['importation'] == true) {
            return $data;
        }

        $personModel = new PersonModel();
        $changeTypeModel = new ChangeTypeModel();
        $changeModel = new ChangeModel();

        
        foreach ($data['id'] as $id) {
            $oldValue = $this->oldValues[$id];
            $newValue = $this->find($id);

            // Log the membership end if the person was soft deleted
            if (empty($oldValue['date_delete']) && !empty($newValue['date_delete'])) {
                $changeTypeId = $changeTypeModel->getChangeTypeId('membership_end');
                
                $changeData = [
                    'fk_change_author' => session()->get('user_id'),
                    'fk_person_concerned' => $id,
                    'fk_change_type' => $changeTypeId,
                    
                    // Concatenate the membership end date and reason in a single string
                    'value_old' => (!empty($oldValue['membership_end']) ? $oldValue['membership_end'].' - '.$oldValue['membership_end_reason'] : ''),
                    'value_new' => (!empty($newValue['membership_end']) ? $newValue['membership_end'].' - '.$newValue['membership_end_reason'] : ''),
                ];
                $changeModel->insert($changeData);
            }

            // (Re)log the membership start if the soft delete status has been removed
            if (!empty($oldValue['date_delete']) && empty($newValue['date_delete'])) {
                $changeTypeId = $changeTypeModel->getChangeTypeId('membership_start');
                
                $changeData = [
                    'fk_change_author' => session()->get('user_id'),
                    'fk_person_concerned' => $id,
                    'fk_change_type' => $changeTypeId,
                    'value_old' => (!empty($oldValue['membership_end']) ? $oldValue['membership_end'].' - '.$oldValue['membership_end_reason'] : ''),
                    'value_new' => $newValue['last_name'].' '.$newValue['first_name']."\n".
                                   lang('members_lang.field_membership_start').': '.$newValue['membership_start']."\n".
                                   ($home ? $home['address_line_1']."\n".
                                            $home['address_line_2']."\n".
                                            $home['postal_code'].' '.$home['city']
                                          : ''),
                ];
                $changeModel->insert($changeData);
            }
        }

        return $data;
    }

    /**
     * Callback method to log the deletion of a person
     */
    protected function logDelete(array $data) {
        // Do not log the changes if the importation flag is set
        if (isset($_SESSION['importation']) && $_SESSION['importation'] == true) {
            return $data;
        }

        $changeTypeModel = new ChangeTypeModel();
        $changeModel = new ChangeModel();

        foreach ($data['id'] as $id) {
            $oldValue = $this->oldValues[$id];

            // Log the membership end
            $changeTypeId = $changeTypeModel->getChangeTypeId('membership_end');
            
            $changeData = [
                'fk_change_author' => session()->get('user_id'),
                'fk_person_concerned' => $id,
                'fk_change_type' => $changeTypeId,
                
                // Concatenate the membership end date and reason in a single string
                'value_old' => '',
                'value_new' => (!empty($oldValue['membership_end']) ? $oldValue['membership_end'].' - '.$oldValue['membership_end_reason'] : ''),
            ];
            $changeModel->insert($changeData);
        }

        return $data;
    }
}
?>
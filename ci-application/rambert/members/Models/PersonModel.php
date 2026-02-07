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
     * @param string $where : a where clause to filter the persons
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
     * Get an array of persons filtered by theyre lastname
     * 
     * @param string $lastname : the lastname to filter by
     * @param bool $withDeleted : A boolean to include or not the soft deleted persons
     * 
     * @return : An array of persons with all attributes, filtered by the lastname
     */
    public function getByLastname(string $lastname, bool $withDeleted = false) {
        $builder = $this->builder();
        $builder->select('*');
        $builder->orderBy("last_name", "ASC");
        if(!$withDeleted) {
            $builder->where('date_delete IS NULL');
        }
        $builder->like('last_name', $lastname);

        $query = $builder->get();
        return $query->getResult('array');
    }

    /**
     * Get an array of persons filtered by text corresponding to theyre
     * lastname, firstname or email.
     * 
     * @param string $text : the text to search for
     * @param bool $withDeleted : A boolean to include or not the soft deleted persons
     * 
     * @return : An array of persons with all attributes, filtered by the text
     */
    public function getByText(string $text, bool $withDeleted = false) {
        $builder = $this->builder();
        $builder->select('*');
        if(!$withDeleted) {
            $builder->where('date_delete IS NULL');
        }
        $builder->groupStart()
                ->like('last_name', $text)
                ->orLike('first_name', $text)
                ->orLike('email', $text)
                ->groupEnd();

        $query = $builder->get();
        return $query->getResult('array');
    }

    /**
     * Callback method to store old values before update
     */
    protected function keepOldValues(array $data) {

        // Store old values
        foreach ($data['id'] as $id) {
            $this->oldValues[$id] = $this->withDeleted()->find($id);
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
        $categoryModel = new CategoryModel();
        
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

            // Log member name change
            if ($oldValue['first_name'] != $newValue['first_name'] || $oldValue['last_name'] != $newValue['last_name']) {
                $changeTypeId = $changeTypeModel->getChangeTypeId('name');

                $changeData = [
                    'fk_change_author' => session()->get('user_id'),
                    'fk_person_concerned' => $id,
                    'fk_change_type' => $changeTypeId,
                    'value_old' => $oldValue['last_name'].' '.$oldValue['first_name'],
                    'value_new' => $newValue['last_name'].' '.$newValue['first_name'],
                ];

                $changeModel->insert($changeData);
            }

            // Log member email or phone change
            if ($oldValue['email'] != $newValue['email']|| $oldValue['phone_1'] != $newValue['phone_1'] ||
                $oldValue['phone_2'] != $newValue['phone_2']) {
                
                $oldContact = '';
                $newContact = '';

                if ($oldValue['email'] != $newValue['email']) {
                    $oldContact .= lang('members_lang.field_email').': '.$oldValue['email']."\n";
                    $newContact .= lang('members_lang.field_email').': '.$newValue['email']."\n";
                }
                if ($oldValue['phone_1'] != $newValue['phone_1']) {
                    $oldContact .= lang('members_lang.field_phone_1').': '.$oldValue['phone_1']."\n";
                    $newContact .= lang('members_lang.field_phone_1').': '.$newValue['phone_1']."\n";
                }
                if ($oldValue['phone_2'] != $newValue['phone_2']) {
                    $oldContact .= lang('members_lang.field_phone_2').': '.$oldValue['phone_2']."\n";
                    $newContact .= lang('members_lang.field_phone_2').': '.$newValue['phone_2']."\n";
                }

                $changeTypeId = $changeTypeModel->getChangeTypeId('contact_informations');

                $changeData = [
                    'fk_change_author' => session()->get('user_id'),
                    'fk_person_concerned' => $id,
                    'fk_change_type' => $changeTypeId,
                    'value_old' => $oldContact,
                    'value_new' => $newContact,
                ];

                $changeModel->insert($changeData);
            }

            // Log member category change
            if ($oldValue['fk_category'] != $newValue['fk_category']) {
                $oldCategory = '';
                $newCategory = '';

                if (!empty($oldValue['fk_category'])) {
                    $oldCatData = $categoryModel->withDeleted()->find($oldValue['fk_category']);
                    if ($oldCatData) {
                        $oldCategory = $oldCatData['name'].' ('.$oldCatData['description'].')';
                    }
                }

                if (!empty($newValue['fk_category'])) {
                    $newCatData = $categoryModel->withDeleted()->find($newValue['fk_category']);
                    if ($newCatData) {
                        $newCategory = $newCatData['name'].' ('.$newCatData['description'].')';
                    }
                }

                $changeTypeId = $changeTypeModel->getChangeTypeId('category');

                $changeData = [
                    'fk_change_author' => session()->get('user_id'),
                    'fk_person_concerned' => $id,
                    'fk_change_type' => $changeTypeId,
                    'value_old' => $oldCategory,
                    'value_new' => $newCategory,
                ];
                $changeModel->insert($changeData);
            }

            // Log member other informations change
            if ($oldValue['birth'] != $newValue['birth']|| $oldValue['profession'] != $newValue['profession'] ||
                $oldValue['godfathers'] != $newValue['godfathers'] ||
                $oldValue['comments'] != $newValue['comments']) {

                $oldInformations = '';
                $newInformations = '';

                if ($oldValue['birth'] != $newValue['birth']) {
                    $oldInformations .= lang('members_lang.field_birth').': '.$oldValue['birth']."\n";
                    $newInformations .= lang('members_lang.field_birth').': '.$newValue['birth']."\n";
                }
                if ($oldValue['profession'] != $newValue['profession']) {
                    $oldInformations .= lang('members_lang.field_profession').': '.$oldValue['profession']."\n";
                    $newInformations .= lang('members_lang.field_profession').': '.$newValue['profession']."\n";
                }
                if ($oldValue['godfathers'] != $newValue['godfathers']) {
                    $oldInformations .= lang('members_lang.field_godfathers').': '.$oldValue['godfathers']."\n";
                    $newInformations .= lang('members_lang.field_godfathers').': '.$newValue['godfathers']."\n";
                }
                if ($oldValue['comments'] != $newValue['comments']) {
                    $oldInformations .= lang('members_lang.field_comments').': '.$oldValue['comments']."\n";
                    $newInformations .= lang('members_lang.field_comments').': '.$newValue['comments']."\n";
                }

                $changeTypeId = $changeTypeModel->getChangeTypeId('other_informations');

                $changeData = [
                    'fk_change_author' => session()->get('user_id'),
                    'fk_person_concerned' => $id,
                    'fk_change_type' => $changeTypeId,
                    'value_old' => $oldInformations,
                    'value_new' => $newInformations,
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
                'value_old' => $oldValue['last_name'].' '.$oldValue['first_name']."\n".
                               lang('members_lang.field_membership_start').': '.$oldValue['membership_start'],
                'value_new' => (!empty($oldValue['membership_end']) ? lang('members_lang.field_membership_end').": ".$oldValue['membership_end']. "\n".
                                                                      $oldValue['membership_end_reason']
                                                                    : ''),
            ];
            $changeModel->insert($changeData);
        }

        return $data;
    }
}
?>
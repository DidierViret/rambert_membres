<?php
/**
 * Model used to manage access informations.
 * These informations are in both access and person tables.
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
namespace Access\Models;

use CodeIgniter\Model;
use Members\Models\PersonModel;
use Members\Models\ChangeTypeModel;
use Members\Models\ChangeModel;
use Access\Models\AccessLevelModel;

class AccessModel extends Model {
    protected $table      = 'access';
    protected $primaryKey = 'id';

    protected $allowedFields = ['fk_access_level', 'fk_person', 'password', 'date_delete'];

    protected $useSoftDeletes = true;
    protected $deletedField  = 'date_delete';

    // Callbacks
    protected $afterFind = ['appendPerson', 'appendAccessLevel'];
    protected $beforeInsert = ['hashPassword', 'unsetPerson', 'unsetAccessLevel'];
    protected $afterInsert = ['logCreate'];
    protected $beforeUpdate = ['hashPassword', 'unsetPerson', 'unsetAccessLevel', 'keepOldValues'];
    protected $afterUpdate = ['logUpdate'];
    protected $beforeDelete = ['keepOldValues'];
    protected $afterDelete = ['logDelete'];

    // Variables used in callbacks
    private $oldValues = [];

    // Declare variables for validation
    protected $validationRules;
    protected $validationMessages;

    /**
     * Called just after the Model's constructor
     */
    protected function initialize() {
        $this->accessLevelModel = new AccessLevelModel();
        $this->personModel = new PersonModel();

        $this->validationRules = [
            'id' => [
                'rules' => 'permit_empty|numeric'
            ],
            'fk_access_level' =>
                ['label' => lang('access_lang.field_access_level'),
                 'rules' => 'required'],
            'password' =>
                ['label' => lang('access_lang.field_password'),
                 'rules' => 'required|trim|'.
                            'min_length['.config("\Access\Config\AccessConfig")->password_min_length.']|'.
                            'max_length['.config("\Access\Config\AccessConfig")->password_max_length.']|'.
                            'matches[password_confirm]'],
        ];

        $this->validationMessages=[
            'password' =>
                ['matches' => lang('access_lang.msg_error_password_not_matches')],
        ];
    }

    /**
     * Get a list of access objects to display it in a table or dropdown.
     * 
     * @param bool $withDeleted : A boolean to include or not the soft deleted access objects
     * @param string $orderBy : name of the field to use to sort the objects
     * @param string $direction : ASC, DESC or RANDOM, the direction of the sorting
     * 
     * @return : An array of arrays, each representing an access object
     */
    public function getList(bool $withDeleted = false, string $orderBy = 'person.last_name', string $direction = 'ASC') {
        $builder = $this->builder();
        $builder->select('access.id, person.last_name, person.first_name, person.email, access_level.name AS access_level_name');
        $builder->join('access_level', 'access.fk_access_level = access_level.id');
        $builder->join('person', 'access.fk_person = person.id');
        if (!$withDeleted) {
            $builder->where('access.date_delete IS NULL');
        }
        $builder->orderBy("$orderBy", "$direction");
        $query = $builder->get();
        
        return $query->getResult('array');
    }

    /**
     * Verify the identification of a user with his email and password.
     * 
     * @param string $email : The given e-mail address, used as person's identifier
     * @param string $password : The given password, to be checked
     * 
     * @return : False if the access has not been found or if password is not correct
     *           The corresponding access object else.
     */
    public function checkPassword(string $email, string $password) {
        // Find the person corresponding to the given email
        $person = $this->personModel->where('email', $email)->first();
        if (empty($person)) {
            return false;
        }

        // Find the access corresponding to the person found
        $access = $this->where('fk_person', $person['id'])->first();
        if (empty($access)) {
            return false;
        }

        // Check if password is correct
        if(password_verify($password, $access['password'])){
            return $access;
        } else {
            return false;
        }
    }

    /**
     * Callback method to append datas from the linked person table
     */
    protected function appendPerson(array $data) {

        if($data['singleton'] && !empty($data['data'])) {
            // Single item, add datas to it
            $data['data']['person'] = $this->personModel->find($data['data']['fk_person']);

        } elseif (!empty($data['data'])) {
            // Multiple items, add datas to each of them
            foreach ($data['data'] as &$access) {
                $access['person'] = $this->personModel->find($access['fk_person']);
            }
        }
        return $data;
    }

    /**
     * Callback method to remove datas from the linked person table before updating or inserting it
     */
    protected function unsetPerson(array $data) {
        if (isset($data['data']['person'])) {
            unset($data['data']['person']);
        }
        return $data;
    }

    /**
     * Callback method to append datas from the linked access_level table
     */
    protected function appendAccessLevel(array $data) {

        if($data['singleton'] && !empty($data['data'])) {
            // Single item, add datas to it
            $data['data']['access_level'] = $this->accessLevelModel->find($data['data']['fk_access_level']);

        } elseif (!empty($data['data'])) {
            // Multiple items, add datas to each of them
            foreach ($data['data'] as &$access) {
                $access['access_level'] = $this->accessLevelModel->find($access['fk_access_level']);
            }
        }
        return $data;
    }

    /**
     * Callback method to remove access_level datas before updating or inserting it
     */
    protected function unsetAccessLevel(array $data) {
        if (isset($data['data']['access_level'])) {
            unset($data['data']['access_level']);
        }
        return $data;
    }

    /**
     * Callback method to hash the password before inserting or updating it
     */
    protected function hashPassword(array $data) {
        if (! isset($data['data']['password'])) {
            // There is no password to insert or update
            return $data;
        } else {
            // Replace the clear password with a hashed password
            $data['data']['password'] = password_hash($data['data']['password'], config('\Access\Config\AccessConfig')->password_hash_algorithm);
            return $data;
        }
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
     * Callback method to log the creation of a new access
     */
    protected function logCreate(array $data) {
        // Do not log the changes if the importation flag is set
        if (isset($_SESSION['importation']) && $_SESSION['importation'] == true) {
            return $data;
        }

        $personModel = new PersonModel();
        $accessLevelModel = new AccessLevelModel();
        $changeTypeModel = new ChangeTypeModel();
        $changeModel = new ChangeModel();

        if ($data['id'] != 0) {
            $newAccess = $data['data'];
            $person = $personModel->find($newAccess['fk_person']);
            $accessLevel = $accessLevelModel->find($newAccess['fk_access_level']);

            $changeTypeId = $changeTypeModel->getChangeTypeId('access');
            
            $changeData = [
                'fk_change_author' => session()->get('user_id'),
                'fk_person_concerned' => $person['id'],
                'fk_change_type' => $changeTypeId,
                'value_old' => lang('access_lang.no_access'),
                'value_new' => $accessLevel['name'],
            ];
            $changeModel->insert($changeData);
        }

        return $data;
    }

    /**
     * Callback method to log the update of an access
     */
    protected function logUpdate(array $data) {
        // Do not log the changes if the importation flag is set
        if (isset($_SESSION['importation']) && $_SESSION['importation'] == true) {
            return $data;
        }

        $personModel = new PersonModel();
        $accessLevelModel = new AccessLevelModel();
        $changeTypeModel = new ChangeTypeModel();
        $changeModel = new ChangeModel();

        foreach ($data['id'] as $id) {
            $oldAccess = $this->oldValues[$id];
            $newAccess = $this->find($id);
            $person = $personModel->find($newAccess['fk_person']);
            $accessLevel = $accessLevelModel->find($newAccess['fk_access_level']);

            $changeTypeId = $changeTypeModel->getChangeTypeId('access');

            // If the access level has changed, log it
            if ($oldAccess['fk_access_level'] != $newAccess['fk_access_level']) {
                // Get the old access level name
                if (!empty($oldAccess['fk_access_level'])) {
                    // There is an old access level, get its name
                    $oldAccessLevelName = $accessLevelModel->find($oldAccess['fk_access_level'])['name'];
                } else {
                    // There is no old access level, get its name from the model
                    $oldAccessLevelName = lang('access_lang.no_access');
                }

                // Get the new access level name
                if (!empty($newAccess['fk_access_level'])) {
                    // There is a new access level, get its name
                    $newAccessLevelName = $accessLevelModel->find($newAccess['fk_access_level'])['name'];
                } else {
                    // There is no new access level, get its name from the model
                    $newAccessLevelName = lang('access_lang.no_access');
                }

                // Log the change
                $changeData = [
                    'fk_change_author' => session()->get('user_id'),
                    'fk_person_concerned' => $person['id'],
                    'fk_change_type' => $changeTypeId,
                    'value_old' => $oldAccessLevelName,
                    'value_new' => $newAccessLevelName,
                ];
                $changeModel->insert($changeData);
            }

            // if the password has changed, log it
            if ($oldAccess['password'] != $newAccess['password']) {

                $changeData = [
                    'fk_change_author' => session()->get('user_id'),
                    'fk_person_concerned' => $person['id'],
                    'fk_change_type' => $changeTypeId,
                    'value_old' => lang('access_lang.old_password'),
                    'value_new' => lang('access_lang.new_password'),
                ];
                $changeModel->insert($changeData); 
            }
        }

        return $data;
    }

    /**
     * Callback method to log the deletion of an access
     */
    protected function logDelete(array $data) {
        // Do not log the changes if the importation flag is set
        if (isset($_SESSION['importation']) && $_SESSION['importation'] == true) {
            return $data;
        }

        $personModel = new PersonModel();
        $accessLevelModel = new AccessLevelModel();
        $changeTypeModel = new ChangeTypeModel();
        $changeModel = new ChangeModel();

        foreach ($data['id'] as $id) {
            $oldAccess = $this->oldValues[$id];
            $person = $personModel->find($oldAccess['fk_person']);
            $accessLevel = $accessLevelModel->find($oldAccess['fk_access_level']);

            $changeTypeId = $changeTypeModel->getChangeTypeId('access');
            
            $changeData = [
                'fk_change_author' => session()->get('user_id'),
                'fk_person_concerned' => $person['id'],
                'fk_change_type' => $changeTypeId,
                'value_old' => $accessLevel['name'],
                'value_new' => lang('access_lang.no_access'),
            ];
            $changeModel->insert($changeData);
        }

        return $data;
    }  
}
?>
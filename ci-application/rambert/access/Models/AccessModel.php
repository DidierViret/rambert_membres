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
    protected $beforeUpdate = ['hashPassword', 'unsetPerson', 'unsetAccessLevel'];

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
}
?>
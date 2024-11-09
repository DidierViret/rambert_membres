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
    protected $afterFind = ['appendLinkedPerson', 'appendLinkedAccessLevel'];

    /* Define fields validation rules
                    $validation_rules=[
                        'username'=>[
                        'label' => 'user_lang.field_username',
                        'rules' => 'trim|required|'
                            . 'min_length['.config("\User\Config\UserConfig")->username_min_length.']|'
                            . 'max_length['.config("\User\Config\UserConfig")->username_max_length.']'],
                        'password'=>[
                            'label' => 'user_lang.field_password',
                            'rules' => 'trim|required|'
                                . 'min_length['.config("\User\Config\UserConfig")->password_min_length.']|'
                                . 'max_length['.config("\User\Config\UserConfig")->password_max_length.']'
                        ]
                    ];
    */

    /**
     * Called just after the Model's constructor
     */
    protected function initialize() {
        $this->accessLevelModel = new AccessLevelModel();
        $this->personModel = new PersonModel();
    }

    /**
     * Callback method to append datas from the linked person table
     */
    protected function appendLinkedPerson(array $data) {

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
     * Callback method to append datas from the linked access_level table
     */
    protected function appendLinkedAccessLevel(array $data) {

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
}
?>
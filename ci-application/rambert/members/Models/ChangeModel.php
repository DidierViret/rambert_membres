<?php
/**
 * Model used to manage changes
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
namespace Members\Models;

use CodeIgniter\Model;
use Members\Models\ChangeTypeModel;
use Members\Models\PersonModel;

class ChangeModel extends Model {
    protected $table      = 'change';
    protected $primaryKey = 'id';

    protected $allowedFields = ['fk_change_author', 'fk_person_concerned', 'fk_change_type', 'value_old', 'value_new', 'date'];

    protected $useSoftDeletes = false;

    // Callbacks
    protected $afterFind = ['appendAuthor', 'appendPerson', 'appendChangeType'];

    public function initialize()
    {
        $this->personModel = new PersonModel();
        $this->changeTypeModel = new ChangeTypeModel();
    }

    /**
     * Callback method to append datas of the author of the change
     */
    protected function appendAuthor(array $data) {

        if($data['singleton'] && !empty($data['data'])) {
            // Single item, add datas to it
            if (!empty($data['data']['fk_change_author'])) {
                $data['data']['author'] = $this->personModel->find($data['data']['fk_change_author']);
            }

        } elseif (!empty($data['data'])) {
            // Multiple items, add datas to each of them
            foreach ($data['data'] as &$change) {
                if (!empty($change['fk_change_author'])) {
                    $change['author'] = $this->personModel->find($change['fk_change_author']);
                }
            }
        }
        return $data;
    }
    
    /**
     * Callback method to append datas of the person concerned by the change
     */
    protected function appendPerson(array $data) {

        if($data['singleton'] && !empty($data['data'])) {
            // Single item, add datas to it
            if (!empty($data['data']['fk_person_concerned'])) {
                $data['data']['person'] = $this->personModel->find($data['data']['fk_person_concerned']);
            }

        } elseif (!empty($data['data'])) {
            // Multiple items, add datas to each of them
            foreach ($data['data'] as &$change) {
                if (!empty($change['fk_person_concerned'])) {
                    $change['person'] = $this->personModel->find($change['fk_person_concerned']);
                }
            }
        }
        return $data;
    }

    /**
     * Callback method to append datas of the linked change type
     */
    protected function appendChangeType(array $data) {

        if($data['singleton'] && !empty($data['data'])) {
            // Single item, add datas to it
            if (!empty($data['data']['fk_change_type'])) {
                $data['data']['change_type'] = $this->changeTypeModel->find($data['data']['fk_change_type']);
            }

        } elseif (!empty($data['data'])) {
            // Multiple items, add datas to each of them
            foreach ($data['data'] as &$change) {
                if (!empty($change['fk_change_type'])) {
                    $change['change_type'] = $this->changeTypeModel->find($change['fk_change_type']);
                }
            }
        }
        return $data;
    }
}
?>
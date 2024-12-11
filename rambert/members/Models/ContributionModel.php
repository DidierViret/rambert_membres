<?php
/**
 * Model used to manage members contributions
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
namespace Members\Models;

use CodeIgniter\Model;
use Members\Models\PersonModel;
use Members\Models\RoleModel;

class ContributionModel extends Model {
    protected $table      = 'contribution';
    protected $primaryKey = 'id';

    protected $allowedFields = ['fk_person', 'fk_role', 'date_begin', 'date_end'];

    protected $useSoftDeletes = false;

    // Callbacks
    protected $afterFind = ['appendPerson', 'appendRole'];

    public function initialize()
    {
        $this->personModel = new PersonModel();
        $this->roleModel = new RoleModel();
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
            foreach ($data['data'] as &$contribution) {
                $contribution['person'] = $this->personModel->find($contribution['fk_person']);
            }
        }
        return $data;
    }

    /**
     * Callback method to append datas from the linked role table
     */
    protected function appendRole(array $data) {

        if($data['singleton'] && !empty($data['data'])) {
            // Single item, add datas to it
            $data['data']['role'] = $this->roleModel->find($data['data']['fk_role']);

        } elseif (!empty($data['data'])) {
            // Multiple items, add datas to each of them
            foreach ($data['data'] as &$contribution) {
                $contribution['role'] = $this->roleModel->find($contribution['fk_role']);
            }
        }
        return $data;
    }
}
?>
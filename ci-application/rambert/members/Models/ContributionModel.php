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
            if (!empty($data['data']['fk_person'])) {
                $data['data']['person'] = $this->personModel->find($data['data']['fk_person']);
            }

        } elseif (!empty($data['data'])) {
            // Multiple items, add datas to each of them
            foreach ($data['data'] as &$contribution) {
                if (!empty($contribution['fk_person'])) {
                    $contribution['person'] = $this->personModel->find($contribution['fk_person']);
                }
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
            if(!empty($data['data']['fk_role'])) {
                $data['data']['role'] = $this->roleModel->find($data['data']['fk_role']);
            }

        } elseif (!empty($data['data'])) {
            // Multiple items, add datas to each of them
            foreach ($data['data'] as &$contribution) {
                if(!empty($contribution['fk_role'])) {
                    $contribution['role'] = $this->roleModel->find($contribution['fk_role']);
                }
            }
        }
        return $data;
    }

    /**
     * Get an array of the contributions of one given person, ordered by a field.
     * 
     * @param int $personId : The id of the person to get the contributions from
     * @param bool $withPassed : A boolean to include or not the passed contributions
     * @param string $orderBy : name of the field to use to sort the contributions
     * @param string $direction : ASC, DESC or RANDOM, the direction of the sorting
     * 
     * @return : An array of contributions with all attributes, ordered by the mentioned field
     */
    public function getOrdered(int $personId, bool $withPassed = false, string $orderBy = 'id', string $direction = 'ASC') {
        $builder = $this->builder();
        $builder->select('*');
        $builder->orderBy("$orderBy", "$direction");
        $builder->where('fk_person', $personId);
        if(!$withPassed) {
            $builder->where('date_end IS NULL');
        }

        $query = $builder->get();
        $contributions = $query->getResult('array');
        foreach($contributions as &$contribution) {
            $contribution['role'] = $this->roleModel->find($contribution['fk_role']);
        }

        return $contributions;
    }
}
?>
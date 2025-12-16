<?php
/**
 * Model used to manage members roles informations
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
namespace Members\Models;

use CodeIgniter\Model;
use Members\Models\TeamModel;

class RoleModel extends Model {
    protected $table      = 'role';
    protected $primaryKey = 'id';

    protected $allowedFields = ['fk_team', 'name', 'description', 'date_delete'];

    protected $useSoftDeletes = true;
    protected $deletedField  = 'date_delete';

    // Callbacks
    protected $afterFind = ['appendTeam'];

    public function initialize()
    {
        $this->teamModel = new TeamModel();
    }

    /**
     * Get an array of roles to display them in a dropdown.
     * 
     * @param bool $withDeleted : A boolean to include or not the soft deleted roles
     * @param string $orderBy : name of the field to use to sort the objects
     * @param string $direction : ASC, DESC or RANDOM, the direction of the sorting
     * 
     * @return : An array with roles ids as keys and roles names as values
     */
    public function getDropdown(bool $withDeleted = false, string $orderBy = 'id', string $direction = 'ASC') {
        $builder = $this->builder();
        $builder->select('id, name');
        $builder->orderBy("$orderBy", "$direction");
        $query = $builder->get();
        
        $array = [];
        foreach ($query->getResult('array') as $row) {
            $array[$row['id']] = $row['name'];
        }

        return $array;
    }

    /**
     * Callback method to append datas from the linked team table
     */
    protected function appendTeam(array $data) {

        if($data['singleton'] && !empty($data['data'])) {
            // Single item, add datas to it
            if (!empty($data['data']['fk_team'])) {
                $data['data']['team'] = $this->teamModel->find($data['data']['fk_team']);
            }

        } elseif (!empty($data['data'])) {
            // Multiple items, add datas to each of them
            foreach ($data['data'] as &$role) {
                if (!empty($role['fk_team'])) {
                    $role['team'] = $this->teamModel->find($role['fk_team']);
                }
            }
        }
        return $data;
    }
}
?>
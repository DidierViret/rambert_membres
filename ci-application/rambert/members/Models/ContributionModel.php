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
    protected $afterInsert = ['logCreate'];
    protected $beforeUpdate = ['keepOldValues'];
    protected $afterUpdate = ['logUpdate'];
    protected $beforeDelete = ['keepOldValues'];
    protected $afterDelete = ['logDelete'];

    // Variables used in callbacks
    private $oldValues = [];


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

        $roleModel = new RoleModel();
        $teamModel = new TeamModel();
        $changeTypeModel = new ChangeTypeModel();
        $changeModel = new ChangeModel();

        if ($data['id'] != 0) {
            $newContribution = $data['data'];
            $role = $roleModel->find($newContribution['fk_role']);
            $team = $teamModel->find($role['fk_team']);

            $changeTypeId = $changeTypeModel->getChangeTypeId('contribution');
            
            $changeData = [
                'fk_change_author' => session()->get('user_id'),
                'fk_person_concerned' => $newContribution['fk_person'],
                'fk_change_type' => $changeTypeId,
                'value_old' => '',
                'value_new' => ($team ? $team['name'].' : ' : '').$role['name']."\n".
                               ($newContribution['date_begin'] ? date('Y', strtotime($newContribution['date_begin'])) : '?')." - ".
                               ($newContribution['date_end'] ? date('Y', strtotime($newContribution['date_end'])) : '?'),
            ];
            $changeModel->insert($changeData);
        }

        return $data;
    }

    /**
     * Callback method to log updates made to a contribution
     */
    protected function logUpdate(array $data) {
        // Do not log the changes if the importation flag is set
        if (isset($_SESSION['importation']) && $_SESSION['importation'] == true) {
            return $data;
        }

        $roleModel = new RoleModel();
        $teamModel = new TeamModel();
        $changeTypeModel = new ChangeTypeModel();
        $changeModel = new ChangeModel();

        foreach ($data['id'] as $index => $id) {
            $oldContribution = $this->oldValues[$id];
            $newContribution = $this->find($id);
            $oldRole = $roleModel->find($oldContribution['fk_role']);
            $newRole = $roleModel->find($newContribution['fk_role']);
            $oldTeam = $teamModel->find($oldRole['fk_team']);
            $newTeam = $teamModel->find($newRole['fk_team']);

            $changeTypeId = $changeTypeModel->getChangeTypeId('contribution');
            
            $changeData = [
                'fk_change_author' => session()->get('user_id'),
                'fk_person_concerned' => $newContribution['fk_person'],
                'fk_change_type' => $changeTypeId,
                'value_old' => ($oldTeam ? $oldTeam['name'].' : ' : '').$oldRole['name']."\n".
                               ($oldContribution['date_begin'] ? date('Y', strtotime($oldContribution['date_begin'])) : '?')." - ".
                               ($oldContribution['date_end'] ? date('Y', strtotime($oldContribution['date_end'])) : '?'),
                'value_new' => ($newTeam ? $newTeam['name'].' : ' : '').$newRole['name']."\n".
                               ($newContribution['date_begin'] ? date('Y', strtotime($newContribution['date_begin'])) : '?')." - ".
                               ($newContribution['date_end'] ? date('Y', strtotime($newContribution['date_end'])) : '?'),
            ];
            $changeModel->insert($changeData);
        }

        return $data;
    }

    /**
     * Callback method to log a contribution deletion
     */
    protected function logDelete(array $data) {
        // Do not log the changes if the importation flag is set
        if (isset($_SESSION['importation']) && $_SESSION['importation'] == true) {
            return $data;
        }

        $roleModel = new RoleModel();
        $teamModel = new TeamModel();
        $changeTypeModel = new ChangeTypeModel();
        $changeModel = new ChangeModel();

        foreach ($data['id'] as $index => $id) {
            $oldContribution = $this->oldValues[$id];
            $oldRole = $roleModel->find($oldContribution['fk_role']);
            $oldTeam = $teamModel->find($oldRole['fk_team']);

            $changeTypeId = $changeTypeModel->getChangeTypeId('contribution');
            
            $changeData = [
                'fk_change_author' => session()->get('user_id'),
                'fk_person_concerned' => $oldContribution['fk_person'],
                'fk_change_type' => $changeTypeId,
                'value_old' => ($oldTeam ? $oldTeam['name'].' : ' : '').$oldRole['name']."\n".
                               ($oldContribution['date_begin'] ? date('Y', strtotime($oldContribution['date_begin'])) : '?')." - ".
                               ($oldContribution['date_end'] ? date('Y', strtotime($oldContribution['date_end'])) : '?'),
                'value_new' => '',
            ];
            $changeModel->insert($changeData);
        }

        return $data;
    }
}
?>
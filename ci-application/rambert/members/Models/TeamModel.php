<?php
/**
 * Model used to manage members team informations
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
namespace Members\Models;

use CodeIgniter\Model;

class TeamModel extends Model {
    protected $table      = 'team';
    protected $primaryKey = 'id';

    protected $allowedFields = ['name', 'description', 'date_delete'];

    protected $useSoftDeletes = true;
    protected $deletedField  = 'date_delete';

    // Declare variables for validation
    protected $validationRules;
    protected $validationMessages;

    /**
     * Called just after the Model's constructor
     */
    protected function initialize() {
        $this->validationRules = [
            'id' => [
                'rules' => 'permit_empty|is_natural'
            ],
            'name' =>
                ['label' => lang('members_lang.col_name'),
                 'rules' => 'required|max_length[100]|is_unique[team.name,id,{id}]'],
            'description' =>
                ['label' => lang('members_lang.col_description'),
                 'rules' => 'permit_empty|max_length[255]'],
        ];

        $this->validationMessages = [
            'name' =>
                ['is_unique' => lang('members_lang.msg_error_team_name_exists')],
        ];
    }

    /**
     * Get an array of teams to display them in a dropdown. Add an empty value at the beginning of the list.
     * 
     * @param bool $withDeleted : A boolean to include or not the soft deleted teams
     * @param string $orderBy : name of the field to use to sort the objects
     * @param string $direction : ASC, DESC or RANDOM, the direction of the sorting
     * 
     * @return : An array with teams ids as keys and teams names as values
     */
    public function getDropdown(bool $withDeleted = false, string $orderBy = 'id', string $direction = 'ASC') {
        $builder = $this->builder();
        $builder->select('id, name');
        $builder->orderBy("$orderBy", "$direction");
        $query = $builder->get();

        // Add an empty value at the beginning of the list
        $array[0] = '---';

        foreach ($query->getResult('array') as $row) {
            $array[$row['id']] = $row['name'];
        }

        return $array;
    }
}
?>
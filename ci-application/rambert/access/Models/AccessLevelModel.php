<?php
/**
 * Model used to manage access level informations
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
namespace Access\Models;

use CodeIgniter\Model;

class AccessLevelModel extends Model {
    protected $table      = 'access_level';
    protected $primaryKey = 'id';

    protected $allowedFields = ['date_delete'];

    protected $useSoftDeletes = true;
    protected $deletedField  = 'date_delete';

    /**
     * Get an array of access levels to display them in a dropdown.
     * 
     * @param bool $withDeleted : A boolean to include or not the soft deleted access_levels
     * @param string $orderBy : name of the field to use to sort the objects
     * @param string $direction : ASC, DESC or RANDOM, the direction of the sorting
     * 
     * @return : An array with access_level ids as keys and access_level names as values
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
}
?>
<?php
/**
 * Model used to manage members category informations
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
namespace Members\Models;

use CodeIgniter\Model;

class CategoryModel extends Model {
    protected $table      = 'category';
    protected $primaryKey = 'id';

    protected $allowedFields = ['date_delete'];

    protected $useSoftDeletes = true;
    protected $deletedField  = 'date_delete';

    /**
     * Get an array of categories to display them in a dropdown.
     * 
     * @param bool $withDeleted : A boolean to include or not the soft deleted categories
     * @param string $orderBy : name of the field to use to sort the objects
     * @param string $direction : ASC, DESC or RANDOM, the direction of the sorting
     * 
     * @return : An array with categories ids as keys and categories names as values
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
<?php
/**
 * Model used to manage change types
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
namespace Members\Models;

use CodeIgniter\Model;

class ChangeTypeModel extends Model {
    protected $table      = 'change_type';
    protected $primaryKey = 'id';

    protected $allowedFields = ['name', 'description', 'date_delete'];

    protected $useSoftDeletes = true;
    protected $deletedField = 'date_delete';

    /**
     * Get the change type ID from a corresponding short name
     *
     * @param String $name The short name of the change type
     * @return int|null The change type ID or null if the name is not found
     */
    public function getChangeTypeId(String $name) {
        switch ($name) {
            case 'membership_start':
                return 1;
            case 'membership_end':
                return 2;
            case 'category':
                return 3;
            case 'address':
                return 4;
            case 'name':
                return 5;
            case 'contact_informations':
                return 6;
            case 'other_informations':
                return 7;
            case 'password':
                return 8;
            case 'contribution':
                return 9;
            default:
                return null;
        }
    }
}
?>
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
}
?>
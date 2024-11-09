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
}
?>
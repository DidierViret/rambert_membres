<?php
/**
 * Model used to manage access level informations
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
namespace Members\Models;

use CodeIgniter\Model;

class PersonModel extends Model {
    protected $table      = 'person';
    protected $primaryKey = 'id';

    protected $allowedFields = ['date_delete'];

    protected $useSoftDeletes = true;
    protected $deletedField  = 'date_delete';
}
?>
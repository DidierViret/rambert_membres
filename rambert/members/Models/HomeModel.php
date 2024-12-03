<?php
/**
 * Model used to manage home informations
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
namespace Members\Models;

use CodeIgniter\Model;

class HomeModel extends Model
{
    protected $table = 'home';
    protected $primaryKey = 'id';

    // Columns allowed for save operations
    protected $allowedFields = [
        'address_title',
        'address_name',
        'address_line_1',
        'address_line_2',
        'postal_code',
        'city',
        'nb_bulletins',
        'comments',
        'date_delete',
    ];

    // Defines the use of soft_delete feature
    protected $useSoftDeletes = true;
    protected $deletedField = 'date_delete';

    public function initialize()
    {
        // Validation rules
        $this->validationRules = [
            'address_title' => 'permit_empty|max_length[100]',
            'address_name' => 'permit_empty|max_length[100]',
            'address_line_1' => 'permit_empty|max_length[100]',
            'address_line_2' => 'permit_empty|max_length[100]',
            'postal_code' => 'permit_empty|max_length[50]',
            'city' => 'permit_empty|max_length[100]',
            'nb_bulletins' => 'permit_empty|integer',
            'comments' => 'permit_empty',
        ];
    }
}
?>
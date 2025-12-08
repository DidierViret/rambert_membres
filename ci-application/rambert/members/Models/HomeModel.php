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
use Members\Models\PersonModel;
use Members\Models\ChangeTypeModel;
use Members\Models\ChangeModel;

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

    // Callbacks
    protected $beforeUpdate = ['keepOldValues'];
    protected $afterUpdate = ['logUpdate'];

    // Variables used in callbacks
    private $oldValues = [];

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

    /**
     * Callback method to store old values before update
     */
    protected function keepOldValues(array $data) {

        // Store old values
        foreach ($data['id'] as $id) {
            $this->oldValues[$id] = $this->find($id);
        }
        
        return $data;
    }

    /**
     * Callback method to log the update
     */
    protected function logUpdate(array $data) {
        // Do not log the update if the importation flag is set
        if (isset($_SESSION['importation']) && $_SESSION['importation'] == true) {
            return $data;
        }

        $personModel = new PersonModel();
        $changeTypeModel = new ChangeTypeModel();
        $changeModel = new ChangeModel();

        // Log the update if one of the address fields has been modified
        foreach ($data['id'] as $id) {
            $oldValue = $this->oldValues[$id];
            $newValue = $this->find($id);

            // Concatenate the address fields in a single string
            $oldAddress = $oldValue['address_title'] . "\n" . $oldValue['address_name'] . "\n" . $oldValue['address_line_1'] . "\n" . $oldValue['address_line_2'] . "\n" . $oldValue['postal_code'] . ' ' . $oldValue['city'];
            $newAddress = $newValue['address_title'] . "\n" . $newValue['address_name'] . "\n" . $newValue['address_line_1'] . "\n" . $newValue['address_line_2'] . "\n" . $newValue['postal_code'] . ' ' . $newValue['city'];
            
            // If one of the address fields changed, log an address update for each person linked to the home
            if ($oldAddress != $newAddress) {
                $persons = $personModel->where('fk_home', $id)->findAll();

                foreach ($persons as $person) {
                    $changeTypeId = $changeTypeModel->getChangeTypeId('address');
                    $changeData = [
                        'fk_change_author' => session()->get('user_id'),
                        'fk_person_concerned' => $person['id'],
                        'fk_change_type' => $changeTypeId,
                        'field' => lang('members_lang.field_home_address'),
                        'value_old' => $oldAddress,
                        'value_new' => $newAddress,
                    ];
                    $changeModel->insert($changeData);
                }
            }
        }

        return $data;
    }
}
?>
<?php
/**
 * Login, logout, check access
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
namespace Access\Models;

class Access_model extends \CodeIgniter\Model {
    protected $table='access';

    /* Define fields validation rules
                    $validation_rules=[
                        'username'=>[
                        'label' => 'user_lang.field_username',
                        'rules' => 'trim|required|'
                            . 'min_length['.config("\User\Config\UserConfig")->username_min_length.']|'
                            . 'max_length['.config("\User\Config\UserConfig")->username_max_length.']'],
                        'password'=>[
                            'label' => 'user_lang.field_password',
                            'rules' => 'trim|required|'
                                . 'min_length['.config("\User\Config\UserConfig")->password_min_length.']|'
                                . 'max_length['.config("\User\Config\UserConfig")->password_max_length.']'
                        ]
                    ];
    */
}
?>
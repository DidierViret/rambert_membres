<?php
/**
 * Config for access module
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */

namespace Access\Config;

use CodeIgniter\Config\BaseConfig;

class AccessConfig extends BaseConfig
{
    /* Access levels */
    public $access_lvl_manager          =   4;
    public $access_lvl_admin            =   5;
    
    /* Values for validation rules */
    public $email_max_length            =   150;
    public $password_min_length         =   8;
    public $password_max_length         =   72;
    
    /* Other values */
    public $password_hash_algorithm     =   PASSWORD_BCRYPT;
}
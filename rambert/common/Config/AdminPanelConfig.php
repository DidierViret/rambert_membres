<?php
/**
 * Configuration needed to display the admin panel.
 *
 * @author      Didier Viret
 * @link        https://github.com/DidierViret
 * @copyright   Copyright (c), Didier Viret
 */

namespace Common\Config;

class AdminPanelConfig extends \CodeIgniter\Config\BaseConfig
{
    /** Update this array to customize admin pannel tabs for your needs 
     *  Syntax : ['label'=>'tab label','pageLink'=>'tab link']
    */
    public $tabs=[
        ['label'=>'members_lang.title_members_list', 'pageLink'=>'members/admin/list'],
    ];
}
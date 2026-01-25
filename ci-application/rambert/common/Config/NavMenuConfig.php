<?php
/**
 * Configuration needed to display the navigation menu.
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */

namespace Common\Config;

class NavMenuConfig extends \CodeIgniter\Config\BaseConfig
{
    /** Update this array to customize navigation menu tabs for your needs 
     *  Syntax : ['label'=>'tab label','pageLink'=>'tab link']
    */
    public $tabs=[
        ['label'=>'members_lang.tab_members_list', 'pageLink'=>'members'],
        ['label'=>'members_lang.tab_changes', 'pageLink'=>'changes'],
    ];
}
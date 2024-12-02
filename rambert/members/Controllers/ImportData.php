<?php
/**
 * Import datas from the old Joomla/CommunityBuilder database
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
namespace Members\Controllers;

use App\Controllers\BaseController;

class ImportData extends BaseController
{
    /**
     * Import all datas from the old Joomla/CommunityBuiler database
     */
    public function import()
    {
        // Connection to the joomla database
        $joomla = [
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'rambert_joomla',
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => true,
        'charset'  => 'utf8',
        'DBCollat' => 'utf8_general_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 3306,
        ];

        $dbJoomla = \Config\Database::connect($joomla);

        // Get all informations from comprofiler and users tables
        $query = $dbJoomla->query('SELECT *
                                   FROM lyf7s_comprofiler
                                   INNER JOIN lyf7s_users ON lyf7s_comprofiler.user_id=lyf7s_users.id');

        $cbMembers = $query->getResult('array');
        //dd($cbMembers);
    }
}

?>
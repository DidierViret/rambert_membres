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

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Response;
use Psr\Log\LoggerInterface;

use Members\Models\HomeModel;
use Members\Models\PersonModel;

class ImportData extends BaseController
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        // Set Access level before calling parent constructor
        // Public access
        $this->accessLevel = "*";
        parent::initController($request, $response, $logger);

        // Load required helpers

        // Load required models
        $this->homeModel = new HomeModel();
        $this->personModel = new PersonModel();
    }

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
        
        // Insert each member in the new database, splitting informations in different tables
        foreach($cbMembers as $cbMember) {
            $home['address_title'] = $cbMember['cb_titre_envois'];
            $home['address_name'] = $cbMember['cb_destinataires_envois'];
            $home['address_line_1'] = $cbMember['cb_adresse'];
            $home['address_line_2'] = null;
            $home['postal_code'] = $cbMember['cb_codepostal'];
            $home['city'] = $cbMember['cb_localite'];
            $home['nb_bulletins'] = $cbMember['cb_nb_bulletins'];
            $home['comments'] = null;
            
            $homeId = $this->homeModel->insert($home);
        }

        dd($this->homeModel->findAll());

        // TODO : Script qui fait un soft_delete des homes dont tous les membres sont désactivés
    }
}

?>
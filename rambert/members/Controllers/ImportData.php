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
    
        $this->dbJoomla = \Config\Database::connect($joomla);
    }

    /**
     * Import all datas from the old Joomla/CommunityBuiler database
     */
    public function import()
    {
        // Get all informations from comprofiler and users tables
        $query = $this->dbJoomla->query('SELECT *
                                   FROM lyf7s_comprofiler
                                   INNER JOIN lyf7s_users ON lyf7s_comprofiler.user_id=lyf7s_users.id');

        $cbMembers = $query->getResult('array');
        
        //dd($cbMembers);
        
        // Insert each member in the new database, splitting informations in different tables
        foreach($cbMembers as $cbMember) {
            $homeId = $this->importHome($cbMember);

            
        }

        dd($this->homeModel->findAll());

        // TODO : Script qui fait un soft_delete des homes dont tous les membres sont désactivés
    }

    /**
     * Get home informations and check if this home allready exists in DB
     * If not, add the new home in DB, else return the id of existing home
     * 
     * @param $cbMember : The member informations as registered in Joomla DB
     * @return : The id of newly created home or of the corresponding existing home
     */
    protected function importHome($cbMember): int {
        $home['address_title'] = $cbMember['cb_titre_envois'];
        $home['address_name'] = $cbMember['cb_destinataires_envois'];
        $home['address_line_1'] = $cbMember['cb_adresse'];
        $home['address_line_2'] = null;
        $home['postal_code'] = $cbMember['cb_codepostal'];
        $home['city'] = $cbMember['cb_localite'];
        $home['nb_bulletins'] = $cbMember['cb_nb_bulletins'];
        $home['comments'] = null;

        $existingHome = $this->homeModel->where(['address_line_1' => $home['address_line_1'],
                                                 'city' => $home['city'],
                                                 'postal_code' => $home['postal_code']])->first();
        
        if (!empty($existingHome)) {
            // Keep the most relevant informations between existing home and cbMember's home
            if (strlen($home['address_title']) > strlen($existingHome['address_title'])) {
                $existingHome['address_title'] = $home['address_title'];
            }
            if (strlen($home['address_name']) > strlen($existingHome['address_name'])) {
                $existingHome['address_name'] = $home['address_name'];
            }
            if ($home['nb_bulletins'] > $existingHome['nb_bulletins']) {
                $existingHome['nb_bulletins'] = $home['nb_bulletins'];
            }

            // Update the allready existing home and return its id
            return $this->homeModel->save($existingHome);
        } else {
            // Insert the new home and return its id
            return $this->homeModel->save($home);
        }
    }
}

?>
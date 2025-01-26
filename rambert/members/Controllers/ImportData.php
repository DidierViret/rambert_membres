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
use Members\Models\ContributionModel;
use Members\Models\TeamModel;
use Members\Models\RoleModel;
use Access\Models\AccessModel;

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

            // Insert home in DB and get its id
            $homeId = $this->importHome($cbMember);

            // Get the corresponding category id
            switch($cbMember['cb_categorie'])
            {
                case 'junior':
                    $categoryId = 1;
                    break;
                case 'jeune':
                    $categoryId = 2;
                    break;
                case 'actif':
                    $categoryId = 3;
                    break;
                case 'conjoint_d_actif':
                    $categoryId = 4;
                    break;
                case 'honoraire':
                    $categoryId = 5;
                    break;
                case 'coinjoint_d_honoraire':
                    $categoryId = 6;
                    break;
                case 'jubilaire':
                    $categoryId = 7;
                    break;
                case 'honneur':
                    $categoryId = 8;
                    break;
                case 'veuve':
                    $categoryId = 9;
                    break;
            }
            
            // Insert person in DB and get its id
            $person = [];
            $person['id'] = $cbMember['user_id'];
            $person['fk_home'] = $homeId;
            $person['fk_category'] = $categoryId;
            $person['title'] = $cbMember['cb_titre'];
            $person['first_name'] = $cbMember['firstname'];
            $person['last_name'] = $cbMember['lastname'];
            $person['email'] = $cbMember['email'];
            $person['phone_1'] = $cbMember['cb_telmobile'];
            $person['phone_2'] = $cbMember['cb_telfixe'];
            $person['birth'] = $cbMember['cb_naissance'];
            $person['profession'] = $cbMember['cb_profession'];
            $person['godfathers'] = $cbMember['cb_parrains'];
            $person['membership_start'] = $cbMember['cb_entreerambert'];
            $person['membership_end'] = $cbMember['cb_sortierambert'];
            $person['membership_end_reason'] = $cbMember['cb_motifsortie'];
            $person['comments'] = $cbMember['cb_observations'];
            if (!empty($cbMember['cb_sortierambert'])) {
                // Consider the date of deletion as december 31 in the year of membership's end
                $person['date_delete'] = $cbMember['cb_sortierambert']."-12-31 00:00:00";
            }

            // Avoid double entries if this method is called several times
            $existingPerson = $this->personModel->withDeleted()->find($person['id']);
            if (empty($existingPerson)) {
                $this->personModel->insert($person);
            }

            // TODO Import all members contributions when their syntax will be correct in the old Joomla database
            // For now, only import contributions for Didier Viret and Stéphane Besuchet
            if (!empty($cbMember['cb_activites_club']) && $cbMember['cb_activites_club'] != "-") {
                if ($cbMember['user_id'] == 42 || $cbMember['user_id'] == 43) {
                    $this->importContributions($cbMember);
                }
            }
        }

        // Add admin rights to Didier Viret
        $accessModel = new AccessModel();
        $admin['fk_access_level'] = 1;
        $admin['fk_person'] = 42;
        $admin['password'] = "admin1234";
        $admin['password_confirm'] = "admin1234";
        $accessModel->save($admin);

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
            $this->homeModel->save($existingHome);
            return $existingHome['id'];
        } else {
            // Insert the new home and return its id
            return $this->homeModel->insert($home);
        }
    }

    /**
     * Get contribution informations from the Joomla database and process them
     * to feet in the new database.
     * 
     * Contributions syntax to put in the old Joomla database before importing :
     * Team : Role : Year begin - Year end
     * 
     * @param $cbMember : The member informations as registered in Joomla DB
     */
    protected function importContributions($cbMember): void {

        // Transform given text in an array of contributions
        $contributions = preg_split("/\r\n|\n|\r/", $cbMember['cb_activites_club']);
        
        foreach($contributions as $contribution) {
            // Separate the Team, Role and Years parts
            $parts = explode(":", $contribution);
            $teamName = trim($parts[0]);
            $roleName = trim($parts[1]);
            $years = trim($parts[2]);

            // Extract the years
            $years = explode("-", $years);
            $yearBegin = trim($years[0]);
            if (empty($years[1])) {
                $yearEnd = null;
            } else {
                $yearEnd = trim($years[1]);
            }

            // Get the corresponding team or create it if it doesn't exist
            if (!empty($teamName)) {
                $teamModel = new TeamModel();
                $team = $teamModel->where('name', $teamName)->first();
                if (empty($team)) {
                    $team['name'] = $teamName;
                    $teamId = $teamModel->insert($team);
                    $team = $teamModel->find($teamId);
                }
            } else {
                $team = null;
            }

            // Get the corresponding role or create it if it doesn't exist
            if (!empty($roleName)) {
                $roleModel = new RoleModel();

                // If the role is linked to a team, get the team's id
                if (empty($team)) {
                    $teamId = null;
                } else {
                    $teamId = $team['id'];
                }
                
                // Get the role or create it if it doesn't exist
                // If the role is linked to a team, check if it exists in this team or add it in this team
                $role = $roleModel->where(['name' => $roleName, 'fk_team' => $teamId])->first();
                if (empty($role)) {
                    $role['name'] = $roleName;
                    $role['fk_team'] = $teamId;
                    $roleId = $roleModel->insert($role);
                    $role = $roleModel->find($roleId);
                }
            }

            // Add the contribution in the database
            $contributionModel = new ContributionModel();

            // Avoid duplicate entries if the importation method is called several times
            $existingContribution = $contributionModel->where(['fk_person' => $cbMember['user_id'],
                                                              'fk_role' => $role['id'],
                                                              'date_begin' => $yearBegin."-01-01 00:00:00"])->first();
            if (empty($existingContribution)) {
                $contribution = [];
                $contribution['fk_person'] = $cbMember['user_id'];
                $contribution['fk_role'] = $role['id'];
                $contribution['date_begin'] = $yearBegin."-01-01 00:00:00";
                if (empty($yearEnd)) {
                    $contribution['date_end'] = null;
                } else {
                    $contribution['date_end'] = $yearEnd."-12-31 23:59:59";
                }

                $contributionModel->insert($contribution);
            }
        }
    }
}

?>
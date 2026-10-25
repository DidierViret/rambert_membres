<?php
/**
 * Manage teams and roles, for administrators only
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */

namespace Members\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use Members\Models\TeamModel;
use Members\Models\RoleModel;
use Members\Models\ContributionModel;

class TeamsAdmin extends BaseController
{
    // Models used in this class
    protected $teamModel;
    protected $roleModel;
    protected $contributionModel;

    /**
     * Constructor
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Set Access level before calling parent constructor
        // Administrators access only
        $this->accessLevel = config('\Access\Config\AccessConfig')->access_lvl_admin;
        parent::initController($request, $response, $logger);

        // Load required helpers
        helper('form');

        // Load required models
        $this->teamModel = new TeamModel();
        $this->roleModel = new RoleModel();
        $this->contributionModel = new ContributionModel();
    }

    /**
     * Display the selected team with the list of its roles.
     * The team id 0 is used for the roles which are not linked to any team.
     *
     * The archived roles of the selected team are always displayed.
     * The archived teams are displayed in the dropdown if the "with_archived" GET parameter is set.
     *
     * @param int $teamId : The id of the selected team. If null, the roles without team are selected.
     */
    public function teamsAdmin($teamId = null) {
        // Select the roles without team by default
        if (is_null($teamId)) {
            $teamId = 0;
        }

        $data['team'] = ($teamId == 0) ? null : $this->teamModel->withDeleted()->find($teamId);
        if ($teamId != 0 && empty($data['team'])) {
            return redirect()->to('/teams');
        }

        // An archived team can only be selected if the archived teams are displayed
        $withArchived = !empty($this->request->getGet('with_archived')) || !empty($data['team']['date_delete']);
        $data['with_archived'] = $withArchived;

        // Teams dropdown, with the roles without team at the beginning, then the teams ordered by name
        $data['teams'] = [0 => lang('members_lang.no_team')];
        $teams = $this->teamModel->withDeleted($withArchived)->orderBy('name', 'ASC')->findAll();
        foreach ($teams as $team) {
            $data['teams'][$team['id']] = $team['name'].(empty($team['date_delete']) ? '' : ' ('.lang('members_lang.archived').')');
        }

        // Roles of the selected team
        $data['list_title'] = lang('members_lang.title_roles_list');

        $data['columns'] = ['name' => lang('members_lang.col_name'),
                            'description' => lang('members_lang.col_description'),
                            'nb_contributions' => lang('members_lang.col_nb_contributions'),
        ];

        // Active roles first, then the archived roles
        $data['items'] = $this->roleModel->withDeleted()
                                         ->where('fk_team', ($teamId == 0) ? null : $teamId)
                                         ->orderBy('date_delete IS NOT NULL', 'ASC', false)
                                         ->orderBy('name', 'ASC')
                                         ->findAll();
        foreach ($data['items'] as &$role) {
            $role['nb_contributions'] = $this->contributionModel->where('fk_role', $role['id'])->countAllResults();
        }

        $data['primary_key_field']  = 'id';
        $data['btn_create_label']   = lang('members_lang.btn_add_role');
        $data['deleted_field']      = 'date_delete';
        $data['deleted_read_only']  = true;
        $data['url_update']         = 'teams/role/update/';
        $data['url_delete']         = 'teams/role/delete/';
        $data['url_restore']        = 'teams/role/restore/';

        // No role can be added to an archived team
        if (empty($data['team']['date_delete'])) {
            $data['url_create'] = 'teams/role/create/'.$teamId;
        }

        return $this->display_view(['Members\teams_admin', 'Common\items_list'], $data);
    }

    /**
     * Display a form to create a team
     */
    public function teamCreate() {
        $data['title'] = lang('members_lang.title_team_new');
        $data['team'] = ['id' => 0, 'name' => '', 'description' => ''];

        return $this->display_view('Members\team_form', $data);
    }

    /**
     * Display a form to update a team
     */
    public function teamUpdate($id = 0) {
        $team = $this->teamModel->find($id);
        if (empty($team)) {
            return redirect()->to('/teams');
        }

        $data['title'] = lang('members_lang.title_team_update');
        $data['team'] = $team;

        return $this->display_view('Members\team_form', $data);
    }

    /**
     * Create or update a team
     */
    public function teamSave($id = 0) {
        $team = [
            'id'          => $id,
            'name'        => trim($this->request->getPost('name') ?? ''),
            'description' => trim($this->request->getPost('description') ?? ''),
        ];

        if ($id == 0) {
            // Create the team
            $newId = $this->teamModel->insert($team);
        } elseif (!empty($this->teamModel->find($id))) {
            // Update the team
            $this->teamModel->update($id, $team);
            $newId = $id;
        } else {
            // Team not found
            return redirect()->to('/teams');
        }

        // If no error occured, the operation is completed, display the team
        if ($this->teamModel->errors() == null) {
            return redirect()->to('/teams/'.$newId);
        }

        // They were validation errors, display the form again
        $data['title'] = lang('members_lang.title_team_'.($id == 0 ? 'new' : 'update'));
        $data['team'] = $team;
        $data['errors'] = $this->teamModel->errors();

        return $this->display_view('Members\team_form', $data);
    }

    /**
     * Display a confirmation message for the deletion of a team
     */
    public function teamConfirmDelete($id = 0) {
        $team = $this->teamModel->find($id);
        if (empty($team)) {
            return redirect()->to('/teams');
        }

        // Confirmation message, telling that the active roles of the team will be deleted too
        $data['message'] = lang('members_lang.msg_team_confirm_delete')."<br><strong>".esc($team['name'])."</strong>";
        $nbRoles = $this->roleModel->where('fk_team', $id)->countAllResults();
        if ($nbRoles > 0) {
            $data['message'] .= "<br>".lang('members_lang.msg_team_roles_deleted', [$nbRoles]);
        }
        $data['url_yes'] = base_url('/teams/delete/'.$id);
        $data['url_no'] = base_url('/teams/'.$id);

        return $this->display_view('Common\confirm_delete', $data);
    }

    /**
     * Delete a team and its active roles. Like a single role deletion, the roles used
     * in contributions are soft deleted and the unused roles are deleted from the database.
     * The team is soft deleted if it is still linked to soft deleted roles, so that
     * the contributions history keeps its name. Else it is deleted from the database.
     */
    public function teamDelete($id = 0) {
        $team = $this->teamModel->find($id);
        if (empty($team)) {
            return redirect()->to('/teams');
        }

        // Delete the roles and the team in a single transaction,
        // so that the team is not deleted alone if the roles' deletion fails
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($this->roleModel->where('fk_team', $id)->findAll() as $role) {
            $nbContributions = $this->contributionModel->where('fk_role', $role['id'])->countAllResults();
            $this->roleModel->delete($role['id'], ($nbContributions == 0));
        }

        $nbDeletedRoles = $this->roleModel->onlyDeleted()->where('fk_team', $id)->countAllResults();
        $this->teamModel->delete($id, ($nbDeletedRoles == 0));

        $db->transComplete();

        return redirect()->to('/teams');
    }

    /**
     * Display a form to create a role
     *
     * @param int $teamId : The id of the team the role will be linked to by default, 0 for no team
     */
    public function roleCreate($teamId = 0) {
        $data['title'] = lang('members_lang.title_role_new');
        $data['role'] = ['id' => 0, 'fk_team' => $teamId ?: null, 'name' => '', 'description' => ''];
        $data['teams'] = $this->teamModel->getDropdown(false, 'name');

        return $this->display_view('Members\role_form', $data);
    }

    /**
     * Display a form to update a role
     */
    public function roleUpdate($id = 0) {
        $role = $this->roleModel->find($id);
        if (empty($role)) {
            return redirect()->to('/teams');
        }

        $data['title'] = lang('members_lang.title_role_update');
        $data['role'] = $role;
        $data['teams'] = $this->teamModel->getDropdown(false, 'name');

        return $this->display_view('Members\role_form', $data);
    }

    /**
     * Create or update a role
     */
    public function roleSave($id = 0) {
        $role = [
            'fk_team'     => $this->request->getPost('fk_team') ?: null,
            'name'        => trim($this->request->getPost('name') ?? ''),
            'description' => trim($this->request->getPost('description') ?? ''),
        ];

        if ($id == 0) {
            // Create the role
            $this->roleModel->insert($role);
        } elseif (!empty($this->roleModel->find($id))) {
            // Update the role
            $this->roleModel->update($id, $role);
        } else {
            // Role not found
            return redirect()->to('/teams');
        }

        // If no error occured, the operation is completed, display the role's team
        if ($this->roleModel->errors() == null) {
            return redirect()->to('/teams/'.($role['fk_team'] ?? 0));
        }

        // They were validation errors, display the form again
        $data['title'] = lang('members_lang.title_role_'.($id == 0 ? 'new' : 'update'));
        $data['role'] = array_merge($role, ['id' => $id]);
        $data['teams'] = $this->teamModel->getDropdown(false, 'name');
        $data['errors'] = $this->roleModel->errors();

        return $this->display_view('Members\role_form', $data);
    }

    /**
     * Display a confirmation message for the deletion of a role,
     * or an information message if the role can't be deleted
     */
    public function roleConfirmDelete($id = 0) {
        $role = $this->roleModel->find($id);
        if (empty($role)) {
            return redirect()->to('/teams');
        }

        $roleName = (!empty($role['team']) ? $role['team']['name'].' : ' : '').$role['name'];
        $urlTeam = base_url('/teams/'.($role['fk_team'] ?? 0));

        // Confirmation message, telling that the contributions using this role will keep it
        $data['message'] = lang('members_lang.msg_role_confirm_delete')."<br><strong>".esc($roleName)."</strong>";
        $nbContributions = $this->contributionModel->where('fk_role', $id)->countAllResults();
        if ($nbContributions > 0) {
            $data['message'] .= "<br>".lang('members_lang.msg_role_used_in_contributions', [$nbContributions]);
        }
        $data['url_yes'] = base_url('/teams/role/delete/'.$id);
        $data['url_no'] = $urlTeam;

        return $this->display_view('Common\confirm_delete', $data);
    }

    /**
     * Delete a role. A role used in contributions is soft deleted, so that
     * the contributions keep it. An unused role is deleted from the database.
     */
    public function roleDelete($id = 0) {
        $role = $this->roleModel->find($id);
        if (empty($role)) {
            return redirect()->to('/teams');
        }

        $nbContributions = $this->contributionModel->where('fk_role', $id)->countAllResults();
        $this->roleModel->delete($id, ($nbContributions == 0));

        return redirect()->to('/teams/'.($role['fk_team'] ?? 0));
    }

    /**
     * Display a confirmation message for the restoration of an archived team
     */
    public function teamConfirmRestore($id = 0) {
        $team = $this->teamModel->onlyDeleted()->find($id);
        if (empty($team)) {
            return redirect()->to('/teams');
        }

        // Confirmation message, telling that the archived roles of the team won't be restored
        $data['title'] = lang('members_lang.title_team_restore');
        $data['message'] = lang('members_lang.msg_team_confirm_restore')."<br><strong>".esc($team['name'])."</strong>";
        $nbRoles = $this->roleModel->onlyDeleted()->where('fk_team', $id)->countAllResults();
        if ($nbRoles > 0) {
            $data['message'] .= "<br>".lang('members_lang.msg_team_roles_stay_archived', [$nbRoles]);
        }
        $data['url_yes'] = base_url('/teams/restore/'.$id);
        $data['url_no'] = base_url('/teams/'.$id.'?with_archived=1');

        return $this->display_view('Common\confirm_restore', $data);
    }

    /**
     * Restore an archived team. Its archived roles stay archived,
     * they can be restored individually.
     */
    public function teamRestore($id = 0) {
        $team = $this->teamModel->onlyDeleted()->find($id);
        if (empty($team)) {
            return redirect()->to('/teams');
        }

        $this->teamModel->update($id, ['date_delete' => null]);

        return redirect()->to('/teams/'.$id.'?with_archived=1');
    }

    /**
     * Display a confirmation message for the restoration of an archived role
     */
    public function roleConfirmRestore($id = 0) {
        $role = $this->roleModel->onlyDeleted()->find($id);
        if (empty($role)) {
            return redirect()->to('/teams');
        }

        $roleName = (!empty($role['team']) ? $role['team']['name'].' : ' : '').$role['name'];

        // Confirmation message, telling that the archived team of the role will be restored too
        $data['title'] = lang('members_lang.title_role_restore');
        $data['message'] = lang('members_lang.msg_role_confirm_restore')."<br><strong>".esc($roleName)."</strong>";
        if (!empty($role['team']['date_delete'])) {
            $data['message'] .= "<br>".lang('members_lang.msg_role_team_restored');
        }
        $data['url_yes'] = base_url('/teams/role/restore/'.$id);
        $data['url_no'] = base_url('/teams/'.($role['fk_team'] ?? 0).'?with_archived=1');

        return $this->display_view('Common\confirm_restore', $data);
    }

    /**
     * Restore an archived role, and its team if it is archived too
     */
    public function roleRestore($id = 0) {
        $role = $this->roleModel->onlyDeleted()->find($id);
        if (empty($role)) {
            return redirect()->to('/teams');
        }

        // Restore the team and the role in a single transaction,
        // so that the team is not restored alone if the role's restoration fails
        $db = \Config\Database::connect();
        $db->transStart();

        if (!empty($role['team']['date_delete'])) {
            $this->teamModel->update($role['fk_team'], ['date_delete' => null]);
        }
        $this->roleModel->update($id, ['date_delete' => null]);

        $db->transComplete();

        return redirect()->to('/teams/'.($role['fk_team'] ?? 0).'?with_archived=1');
    }
}
?>

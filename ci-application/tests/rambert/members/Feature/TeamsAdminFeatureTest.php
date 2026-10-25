<?php
/**
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\rambert\access\Database\Seeds\AccessTestSeeder;

class TeamsAdminFeatureTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    // Migrations
    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $refresh     = true;
    protected $namespace   = Null;

    // Seeds
    protected $seedOnce = true;
    protected $seed = AccessTestSeeder::class;

    // Session of a logged in administrator
    private $adminSession;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminSession = ['logged_in' => true,
                               'user_id' => 20,
                               'access_level' => config('\Access\Config\AccessConfig')->access_lvl_admin];

        // Clean the teams and roles tables before each test
        $this->db->table('contribution')->emptyTable();
        $this->db->table('role')->emptyTable();
        $this->db->table('team')->emptyTable();
    }

    /**
     * Only administrators can manage teams and roles
     */
    public function testAccessDeniedForManagers()
    {
        $session = $this->adminSession;
        $session['access_level'] = config('\Access\Config\AccessConfig')->access_lvl_manager;

        $this->expectException(\Access\Exceptions\AccessDeniedException::class);
        $this->withSession($session)->get('teams');
    }

    /**
     * Display the selected team with its roles
     */
    public function testTeamsAdminDisplaysSelectedTeamRoles()
    {
        $teamA = $this->createTeam('Team A');
        $teamB = $this->createTeam('Team B');
        $this->createRole($teamA, 'Role of team A');
        $this->createRole($teamB, 'Role of team B');
        $this->createRole(null, 'Role without team');

        // Roles without team are selected by default
        $result = $this->withSession($this->adminSession)->get('teams');
        $result->assertOK();
        $result->assertSeeElement('#team_select');
        $result->assertSee('Role without team');
        $result->assertDontSee('Role of team A');
        $result->assertSeeLink(lang('members_lang.btn_add_role'));

        // Select a team
        $result = $this->withSession($this->adminSession)->get('teams/'.$teamA);
        $result->assertSee('Role of team A');
        $result->assertDontSee('Role of team B');

        // Select another team
        $result = $this->withSession($this->adminSession)->get('teams/'.$teamB);
        $result->assertSee('Role of team B');
        $result->assertDontSee('Role of team A');

        // Roles without team
        $result = $this->withSession($this->adminSession)->get('teams/0');
        $result->assertSee('Role without team');
        $result->assertDontSee('Role of team A');

        // Unknown team redirects to the default team
        $result = $this->withSession($this->adminSession)->get('teams/999999');
        $result->assertRedirectTo('teams');
    }

    /**
     * Create, update and delete a team
     */
    public function testTeamCrud()
    {
        // Create
        $result = $this->withSession($this->adminSession)->post('teams/save/0', ['name' => 'New team', 'description' => 'Desc']);
        $team = $this->db->table('team')->where('name', 'New team')->get()->getRowArray();
        $this->assertNotEmpty($team);
        $result->assertRedirectTo('teams/'.$team['id']);

        // A team name must be unique
        $result = $this->withSession($this->adminSession)->post('teams/save/0', ['name' => 'New team', 'description' => '']);
        $result->assertOK();
        $result->assertSee(lang('members_lang.msg_error_team_name_exists'));

        // Update
        $this->withSession($this->adminSession)->post('teams/save/'.$team['id'], ['name' => 'Renamed team', 'description' => '']);
        $this->seeInDatabase('team', ['id' => $team['id'], 'name' => 'Renamed team']);

        // A team with a used role is soft deleted. Its used role is soft deleted, its unused role is deleted from the database
        $usedRole = $this->createRole($team['id'], 'Used role');
        $unusedRole = $this->createRole($team['id'], 'Unused role');
        $this->createContribution($usedRole);
        $result = $this->withSession($this->adminSession)->get('teams/delete/'.$team['id']);
        $result->assertSeeElement('#delete_confirmation');
        $result->assertSee(lang('members_lang.msg_team_roles_deleted', [2]));
        $result = $this->withSession($this->adminSession)->post('teams/delete/'.$team['id']);
        $result->assertRedirectTo('teams');
        $deletedTeam = $this->db->table('team')->where('id', $team['id'])->get()->getRowArray();
        $this->assertNotEmpty($deletedTeam['date_delete']);
        $deletedRole = $this->db->table('role')->where('id', $usedRole)->get()->getRowArray();
        $this->assertNotEmpty($deletedRole['date_delete']);
        $this->dontSeeInDatabase('role', ['id' => $unusedRole]);
        $this->assertStringNotContainsString('Renamed team', $this->withSession($this->adminSession)->get('teams')->getBody());

        // A team with only soft deleted roles is soft deleted
        $otherTeam = $this->createTeam('Other team');
        $roleId = $this->createRole($otherTeam, 'Role');
        $this->createContribution($roleId);
        $this->withSession($this->adminSession)->post('teams/role/delete/'.$roleId);
        $this->withSession($this->adminSession)->post('teams/delete/'.$otherTeam);
        $deletedTeam = $this->db->table('team')->where('id', $otherTeam)->get()->getRowArray();
        $this->assertNotEmpty($deletedTeam['date_delete']);

        // The name of a soft deleted team can't be used again
        $result = $this->withSession($this->adminSession)->post('teams/save/0', ['name' => 'Renamed team', 'description' => '']);
        $result->assertSee(lang('members_lang.msg_error_team_name_exists'));
        $this->assertEquals(1, $this->db->table('team')->where('name', 'Renamed team')->countAllResults());

        // A team without any role is deleted from the database
        $emptyTeam = $this->createTeam('Empty team');
        $this->withSession($this->adminSession)->post('teams/delete/'.$emptyTeam);
        $this->dontSeeInDatabase('team', ['id' => $emptyTeam]);

        // A team with only unused roles is deleted from the database, with its roles
        $unusedTeam = $this->createTeam('Unused team');
        $unusedRole = $this->createRole($unusedTeam, 'Unused role');
        $this->withSession($this->adminSession)->post('teams/delete/'.$unusedTeam);
        $this->dontSeeInDatabase('role', ['id' => $unusedRole]);
        $this->dontSeeInDatabase('team', ['id' => $unusedTeam]);
    }

    /**
     * Create, update and delete a role
     */
    public function testRoleCrud()
    {
        $teamA = $this->createTeam('Team A');
        $teamB = $this->createTeam('Team B');

        // Create form preselects the given team
        $result = $this->withSession($this->adminSession)->get('teams/role/create/'.$teamA);
        $result->assertOK();
        $this->assertStringContainsString('<option value="'.$teamA.'" selected>', $result->getBody());

        // Create
        $result = $this->withSession($this->adminSession)->post('teams/role/save/0', ['fk_team' => $teamA, 'name' => 'New role', 'description' => '']);
        $result->assertRedirectTo('teams/'.$teamA);
        $role = $this->db->table('role')->where('name', 'New role')->get()->getRowArray();
        $this->assertEquals($teamA, $role['fk_team']);

        // A role name is required
        $result = $this->withSession($this->adminSession)->post('teams/role/save/0', ['fk_team' => $teamA, 'name' => '', 'description' => '']);
        $result->assertOK();
        $result->assertSeeElement('#role_form .alert-danger');

        // Move the role to another team
        $result = $this->withSession($this->adminSession)->post('teams/role/save/'.$role['id'], ['fk_team' => $teamB, 'name' => 'New role', 'description' => '']);
        $result->assertRedirectTo('teams/'.$teamB);
        $this->seeInDatabase('role', ['id' => $role['id'], 'fk_team' => $teamB]);

        // Remove the role from any team
        $result = $this->withSession($this->adminSession)->post('teams/role/save/'.$role['id'], ['fk_team' => 0, 'name' => 'New role', 'description' => '']);
        $result->assertRedirectTo('teams/0');
        $this->seeInDatabase('role', ['id' => $role['id'], 'fk_team' => null]);

        // A role used nowhere is deleted from the database
        $unusedRole = $this->createRole($teamA, 'Unused role');
        $result = $this->withSession($this->adminSession)->get('teams/role/delete/'.$unusedRole);
        $result->assertDontSee(lang('members_lang.msg_role_used_in_contributions', [1]));
        $result = $this->withSession($this->adminSession)->post('teams/role/delete/'.$unusedRole);
        $result->assertRedirectTo('teams/'.$teamA);
        $this->dontSeeInDatabase('role', ['id' => $unusedRole]);

        // A role used in contributions can be deleted, after a confirmation telling it is used
        $this->withSession($this->adminSession)->post('teams/role/save/'.$role['id'], ['fk_team' => $teamA, 'name' => 'New role', 'description' => '']);
        $contributionId = $this->createContribution($role['id']);
        $result = $this->withSession($this->adminSession)->get('teams/role/delete/'.$role['id']);
        $result->assertSeeElement('#delete_confirmation');
        $result->assertSee(lang('members_lang.msg_role_used_in_contributions', [1]));
        $result = $this->withSession($this->adminSession)->post('teams/role/delete/'.$role['id']);
        $result->assertRedirectTo('teams/'.$teamA);

        // The role is soft deleted and stays in the team's roles as an archived role
        $deletedRole = $this->db->table('role')->where('id', $role['id'])->get()->getRowArray();
        $this->assertNotEmpty($deletedRole['date_delete']);
        $this->assertStringContainsString('teams/role/restore/'.$role['id'], $this->withSession($this->adminSession)->get('teams/'.$teamA)->getBody());

        // The contribution keeps its role, in the contributions list and in the contribution form
        $contribution = $this->db->table('contribution')->where('id', $contributionId)->get()->getRowArray();
        $result = $this->withSession($this->adminSession)->get('contributions/'.$contribution['fk_person']);
        $result->assertSee('Team A : New role');
        $result = $this->withSession($this->adminSession)->get('contribution/update/'.$contributionId);
        $result->assertOK();
        $result->assertSee('"name":"New role"');
    }

    /**
     * Display the archived teams and roles, and restore them
     */
    public function testArchivesDisplayAndRestore()
    {
        $activeTeam = $this->createTeam('Active team');
        $archivedTeam = $this->createTeam('Archived team', true);
        $this->createRole($activeTeam, 'Active role');
        $archivedRole = $this->createRole($activeTeam, 'Archived role', true);
        $roleOfArchivedTeam = $this->createRole($archivedTeam, 'Role of archived team', true);
        $otherRoleOfArchivedTeam = $this->createRole($archivedTeam, 'Other role of archived team', true);

        // Archived teams are hidden by default, the archived roles of the selected team are always displayed
        $result = $this->withSession($this->adminSession)->get('teams/'.$activeTeam);
        $result->assertSee('Active role');
        $result->assertSee('Archived role');
        $result->assertDontSee('Archived team');

        // Archived teams are displayed on demand
        $result = $this->withSession($this->adminSession)->get('teams/'.$activeTeam.'?with_archived=1');
        $result->assertSee('Archived team ('.lang('members_lang.archived').')');

        // Archived roles can only be restored
        $body = $result->getBody();
        $this->assertStringContainsString('teams/role/restore/'.$archivedRole, $body);
        $this->assertStringNotContainsString('teams/role/update/'.$archivedRole, $body);
        $this->assertStringNotContainsString('teams/role/delete/'.$archivedRole, $body);

        // An archived team is displayed with the archives. It can only be restored and no role can be added to it
        $result = $this->withSession($this->adminSession)->get('teams/'.$archivedTeam);
        $result->assertOK();
        $result->assertSee('Role of archived team');
        $this->assertStringContainsString('teams/restore/'.$archivedTeam, $result->getBody());
        $this->assertStringNotContainsString('teams/delete/'.$archivedTeam, $result->getBody());
        $result->assertDontSeeLink(lang('members_lang.btn_add_role'));

        // Restore an archived role of an active team
        $result = $this->withSession($this->adminSession)->get('teams/role/restore/'.$archivedRole);
        $result->assertSeeElement('#restore_confirmation');
        $result->assertDontSee(lang('members_lang.msg_role_team_restored'));
        $result = $this->withSession($this->adminSession)->post('teams/role/restore/'.$archivedRole);
        $result->assertRedirectTo('teams/'.$activeTeam.'?with_archived=1');
        $this->seeInDatabase('role', ['id' => $archivedRole, 'date_delete' => null]);

        // Restoring a role of an archived team restores the team too
        $result = $this->withSession($this->adminSession)->get('teams/role/restore/'.$roleOfArchivedTeam);
        $result->assertSee(lang('members_lang.msg_role_team_restored'));
        $this->withSession($this->adminSession)->post('teams/role/restore/'.$roleOfArchivedTeam);
        $this->seeInDatabase('role', ['id' => $roleOfArchivedTeam, 'date_delete' => null]);
        $this->seeInDatabase('team', ['id' => $archivedTeam, 'date_delete' => null]);
        $this->assertNotNull($this->db->table('role')->where('id', $otherRoleOfArchivedTeam)->get()->getRowArray()['date_delete']);

        // Restoring a team doesn't restore its archived roles
        $this->db->table('team')->where('id', $archivedTeam)->update(['date_delete' => date('Y-m-d H:i:s')]);
        $result = $this->withSession($this->adminSession)->get('teams/restore/'.$archivedTeam);
        $result->assertSeeElement('#restore_confirmation');
        $result->assertSee(lang('members_lang.msg_team_roles_stay_archived', [1]));
        $result = $this->withSession($this->adminSession)->post('teams/restore/'.$archivedTeam);
        $result->assertRedirectTo('teams/'.$archivedTeam.'?with_archived=1');
        $this->seeInDatabase('team', ['id' => $archivedTeam, 'date_delete' => null]);
        $this->assertNotNull($this->db->table('role')->where('id', $otherRoleOfArchivedTeam)->get()->getRowArray()['date_delete']);

        // Active teams and roles can't be restored
        $result = $this->withSession($this->adminSession)->get('teams/restore/'.$activeTeam);
        $result->assertRedirectTo('teams');
        $result = $this->withSession($this->adminSession)->post('teams/role/restore/'.$archivedRole);
        $result->assertRedirectTo('teams');
    }

    private function createTeam(string $name, bool $archived = false): int
    {
        $this->db->table('team')->insert(['name' => $name, 'date_delete' => $archived ? date('Y-m-d H:i:s') : null]);
        return $this->db->insertID();
    }

    private function createRole(?int $teamId, string $name, bool $archived = false): int
    {
        $this->db->table('role')->insert(['fk_team' => $teamId, 'name' => $name, 'date_delete' => $archived ? date('Y-m-d H:i:s') : null]);
        return $this->db->insertID();
    }

    private function createContribution(int $roleId): int
    {
        $person = $this->db->table('person')->get()->getRowArray();
        $this->db->table('contribution')->insert(['fk_person' => $person['id'], 'fk_role' => $roleId, 'date_begin' => '2020-01-01']);
        return $this->db->insertID();
    }
}

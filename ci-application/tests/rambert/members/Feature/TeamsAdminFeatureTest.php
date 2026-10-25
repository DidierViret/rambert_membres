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

        // A team with roles can't be deleted
        $roleId = $this->createRole($team['id'], 'Role');
        $result = $this->withSession($this->adminSession)->get('teams/delete/'.$team['id']);
        $result->assertSeeElement('#not_deletable');
        $this->withSession($this->adminSession)->post('teams/delete/'.$team['id']);
        $this->seeInDatabase('team', ['id' => $team['id']]);

        // A team without role can be deleted
        $this->db->table('role')->where('id', $roleId)->delete();
        $result = $this->withSession($this->adminSession)->get('teams/delete/'.$team['id']);
        $result->assertSeeElement('#delete_confirmation');
        $result = $this->withSession($this->adminSession)->post('teams/delete/'.$team['id']);
        $result->assertRedirectTo('teams');
        $this->dontSeeInDatabase('team', ['id' => $team['id']]);
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

        // A role used in contributions can't be deleted
        $contributionId = $this->createContribution($role['id']);
        $result = $this->withSession($this->adminSession)->get('teams/role/delete/'.$role['id']);
        $result->assertSeeElement('#not_deletable');
        $this->withSession($this->adminSession)->post('teams/role/delete/'.$role['id']);
        $this->seeInDatabase('role', ['id' => $role['id']]);

        // A role without contribution can be deleted
        $this->db->table('contribution')->where('id', $contributionId)->delete();
        $result = $this->withSession($this->adminSession)->post('teams/role/delete/'.$role['id']);
        $result->assertRedirectTo('teams/0');
        $this->dontSeeInDatabase('role', ['id' => $role['id']]);
    }

    private function createTeam(string $name): int
    {
        $this->db->table('team')->insert(['name' => $name]);
        return $this->db->insertID();
    }

    private function createRole(?int $teamId, string $name): int
    {
        $this->db->table('role')->insert(['fk_team' => $teamId, 'name' => $name]);
        return $this->db->insertID();
    }

    private function createContribution(int $roleId): int
    {
        $person = $this->db->table('person')->get()->getRowArray();
        $this->db->table('contribution')->insert(['fk_person' => $person['id'], 'fk_role' => $roleId, 'date_begin' => '2020-01-01']);
        return $this->db->insertID();
    }
}

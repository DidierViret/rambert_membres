<?php
/**
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */

namespace Access\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Access\Models\AccessModel;
use Tests\rambert\access\Database\Seeds\AccessTestSeeder;

/**
 * @internal
 */
final class AccessModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    // Migrations
    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $refresh     = true;
    protected $namespace   = Null;

    // Seeds
    protected $seedOnce = true;
    protected $seed = AccessTestSeeder::class;
    
    /**
     * Check that the table is seeded for tests
     */
    public function testAccessFindAll(): void
    {
        $model = new AccessModel();

        // Get every row created by ExampleSeeder
        $objects = $model->findAll();

        // Make sure the count is as expected
        $this->assertCount(2, $objects);
    }

    /**
     * Check that soft_delete is enabled and does work
     */
    public function testSoftDeleteLeavesRow(): void
    {
        $model = new AccessModel();

        // Get an access object and soft delete it
        $access = $model->first();
        $model->delete($access['id']);

        // The model should no longer find it as en active access
        $this->assertNull($model->find($access['id']));

        // But it should still find it as archived
        $this->assertNotNull($model->withDeleted()->find($access['id']));

        // Undelete the soft deleted access
        $model->update($access['id'], ['date_delete' => Null]);
        $this->assertNotNull($model->find($access['id']));
    }

    /**
     * Check that access_level datas are present in access object
     */
    public function testAccessLevelDatas(): void
    {
        $model = new AccessModel();

        // Get an access object
        $access = $model->first();

        // Check that model attached access_level datas
        $this->assertArrayHasKey('access_level', $access);
        $this->assertArrayHasKey('name', $access['access_level']);
        // Check that the attached access_level is the good one
        $this->assertEquals($access['access_level']['id'], $access['fk_access_level']);
    }

    /**
     * Check that related datas from person table are present in access object
     */
    public function testPersonDatas(): void
    {
        $model = new AccessModel();

        // Get an access object
        $access = $model->first();

        // Check that model attached person datas
        $this->assertArrayHasKey('person', $access);
        $this->assertArrayHasKey('fk_home', $access['person']);
        // Check that the attached person is the good one
        $this->assertEquals($access['person']['id'], $access['fk_person']);
    }

    /**
     * Check that password is hashed before storing it in database
     */
    public function testPasswordHash(): void
    {
        $model = new AccessModel();

        // Get an access object
        $access = $model->first();

        // Set a password and save it
        $password = "MySafePassword";
        $data = [
            'password' => $password,
            'password_confirm' => $password
        ];
        $model->update($access['id'], $data);

        // Get updated access object
        $updatedAccess = $model->find($access['id']);

        // Check that password has been saved, is not stored clear, is correctly hashed
        $this->assertNotEmpty($updatedAccess['password']);
        $this->assertNotEquals($updatedAccess['password'], $password);
        $this->assertTrue(password_verify($password, $updatedAccess['password']));

        // Create a new access object with a password and save it
        $newAccess = [
            'fk_access_level' => 2,
            'fk_person' => 20,
            'password' => $password,
            'password_confirm' => $password,
        ];
        $insertedAccess = $model->insert($newAccess);
        $insertedAccess = $model->find($insertedAccess);

        // Check that password has been saved, is not stored clear, is correctly hashed
        $this->assertNotEmpty($insertedAccess['password']);
        $this->assertNotEquals($insertedAccess['password'], $password);
        $this->assertTrue(password_verify($password, $insertedAccess['password']));
    }

    /**
     * Check that the checkpassword method is working
     */
    public function testCheckPassword(): void
    {
        /** TODO : Check that soft deleted access or soft deleted person get "false" in checkPassword */
    }
}

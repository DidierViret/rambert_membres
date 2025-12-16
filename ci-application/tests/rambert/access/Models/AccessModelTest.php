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
use Members\Models\PersonModel;
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

        // Restore the soft deleted access
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

        // Purge the newly created access to avoid effects on other tests
        $model->delete($insertedAccess['id'], true);
    }

    /**
     * Check that the checkpassword method is working
     */
    public function testCheckPassword(): void
    {
        $model = new AccessModel();
        $personModel = new PersonModel();

        // Get an access object
        $access = $model->first();

        // Set a password and save it
        $password = "MySafePassword";
        $data = [
            'password' => $password,
            'password_confirm' => $password
        ];
        $model->update($access['id'], $data);

        // Check that a correct email - password combination returns the right access object
        $checkPasswordResult = $model->checkPassword($access['person']['email'], $password);
        $this->assertNotEmpty($checkPasswordResult);
        $this->assertEquals($checkPasswordResult['id'], $access['id']);

        // Check that a wrong email - password combination returns an empty value
        $checkPasswordResult = $model->checkPassword($access['person']['email'], 'wrongPassword');
        $this->assertEmpty($checkPasswordResult);

        // Check that trying to connect with a soft deleted access returns an empty value
        $model->delete($access['id']);
        $this->assertNotEmpty($model->withDeleted()->find($access['id']));
        $this->assertEmpty($model->checkPassword($access['person']['email'], $password));
        // Restore the soft deleted access
        $model->update($access['id'], ['date_delete' => Null]);
        $this->assertNotNull($model->find($access['id']));

        // Check that trying to connect with a soft deleted person returns an empty value
        $personModel->delete($access['fk_person']);
        $this->assertNotEmpty($personModel->withDeleted()->find($access['fk_person']));
        $this->assertEmpty($model->checkPassword($access['person']['email'], $password));
        // Restore the soft deleted person
        $personModel->update($access['fk_person'], ['date_delete' => Null]);
        $this->assertNotNull($personModel->find($access['fk_person']));
    }
}

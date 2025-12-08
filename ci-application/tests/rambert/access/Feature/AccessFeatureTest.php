<?php
/**
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */
namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\rambert\access\Database\Seeds\AccessTestSeeder;
use Access\Models\AccessModel;

class AccessFeatureTest extends CIUnitTestCase
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

    /**
     * Test the login form in various situations
     */
    public function testLoginForm()
    {
        // Display an empty login form
        $result = $this->get('login');
        $result->assertOK();
        $result->assertDontSeeElement('#error');
        $result->assertSeeElement('#email');
        $result->assertSeeElement('#password');

        // Try a connexion with bad email and password
        $result = $this->post('login', ['email' => 'bad@email.com', 'password' => 'wrongPassword', 'btn_login' => true]);
        $result->assertOK();
        $result->assertSee(lang('access_lang.msg_error_invalid_password'));
        $result->assertSeeElement('#error');
        $result->assertSeeElement('#email');
        $result->assertSeeElement('#password');
    }

    /**
     * Test the "change my password" form in various situations
     */
    public function testChangeMyPasswordForm()
    {
        // Try to display "change my password" form with no user logged in
        // Should redirect to the login page
        $result = $this->withSession()->get('change_my_password');
        $result->assertRedirectTo('login');

        // Simulate a user login
        $accessModel = new AccessModel();
        $access = $accessModel->first();
        $password = 'mySecurePassword';

        $data['password'] = $password;
        $data['password_confirm'] = $password;
        $accessModel->update($access['id'], $data);
        $this->post('login', ['email' => $access['person']['email'],
                              'password' => $password,
                              'btn_login' => true]);

        // Display an empty "change my password" form
        $result = $this->withSession()->get('change_my_password');
        $result->assertOK();
        $result->assertDontSeeElement('#error');
        $result->assertSeeElement('#old_password');
        $result->assertSeeElement('#new_password');
        $result->assertSeeElement('#confirm_password');

        // Try to change password with wrong old password
        $result = $this->withSession()->post('change_my_password', ['old_password' => 'wrongPassword',
                                                                    'new_password' => '1234',
                                                                    'confirm_password' => '1234',
                                                                    'btn_change_password' => true]);
        $result->assertOK();
        $result->assertSee(lang('access_lang.msg_error_invalid_old_password'));
        $result->assertSeeElement('#error');
        $result->assertSeeElement('#old_password');
        $result->assertSeeElement('#new_password');
        $result->assertSeeElement('#confirm_password');

        // Try to change password with too short new password
        $result = $this->withSession()->post('change_my_password', ['old_password' => $password,
                                                                    'new_password' => '1234',
                                                                    'confirm_password' => '1234',
                                                                    'btn_change_password' => true]);
        $result->assertOK();
        $result->assertSeeElement('#error');
        $result->assertSeeElement('#old_password');
        $result->assertSeeElement('#new_password');
        $result->assertSeeElement('#confirm_password');

        // Try to change password with different new_password and confirm_password
        $result = $this->withSession()->post('change_my_password', ['old_password' => $password,
                                                                    'new_password' => 'myNewPassword',
                                                                    'confirm_password' => 'anotherPassword',
                                                                    'btn_change_password' => true]);
        $result->assertOK();
        $result->assertSee(lang('access_lang.msg_error_password_not_matches'));
        $result->assertSeeElement('#error');
        $result->assertSeeElement('#old_password');
        $result->assertSeeElement('#new_password');
        $result->assertSeeElement('#confirm_password');

        // Change a password with right datas and control the success
        $result = $this->withSession()->post('change_my_password', ['old_password' => $password,
                                                                    'new_password' => 'myNewPassword',
                                                                    'confirm_password' => 'myNewPassword',
                                                                    'btn_change_password' => true]);
        $result->assertOK();
        $result->assertNotSeeElement('#old_password');
        
        // Check that the password has been updated
        $this->assertNotEmpty($accessModel->checkPassword($access['person']['email'], 'myNewPassword'));
    }
}
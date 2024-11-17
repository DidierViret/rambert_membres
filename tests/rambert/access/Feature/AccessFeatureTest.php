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
}
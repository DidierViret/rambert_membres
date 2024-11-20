<?php
/**
 * Login, logout, check access
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */

namespace Access\Controllers;

use App\Controllers\BaseController;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Response;
use Psr\Log\LoggerInterface;

use Access\Models\AccessModel;
use Access\Models\Access_level_model;

class Access extends BaseController
{
    /**
     * Constructor
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        // Set Access level before calling parent constructor
        // Public access
        $this->access_level = "*";
        parent::initController($request, $response, $logger);

        // Load required helpers
        helper('form');

        // Load required models
        $this->accessModel = new AccessModel();
    }

    public function test() {
        //$this->accessModel->update(1, ['password' => 'manager1234', 'password_confirm' => 'manager1234']);
        //$this->accessModel->update(2, ['password' => 'admin1234', 'password_confirm' => 'admin1234']);

        dd($this->accessModel->checkPassword('administrator@test.com', 'admin1234'));
    }

    /**
     * Display login form.
     * Check login and password when submitted.
     * Redirect after login success.
     * 
     * @return string|Response : Display the view containing the login form or
     *                           redirect to the after_login_redirect URL
     */
    public function login(): string|Response {

        // Store the redirection URL in a session variable
        if (!is_null($this->request->getVar('after_login_redirect'))) {
            $_SESSION['after_login_redirect'] = $this->request->getVar('after_login_redirect');
        }

        // If no redirection URL is provided or the redirection URL is the
        // login form, redirect to site's root after login
        if (!isset($_SESSION['after_login_redirect'])
                || $_SESSION['after_login_redirect'] == current_url()) {

            $_SESSION['after_login_redirect'] = base_url();
        }

        // If user not yet logged in
        if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {

            // Check if the form has been submitted, else just display the form
            if (!is_null($this->request->getVar('btn_login'))) {

                $email = $this->request->getVar('email');
                $password = $this->request->getvar('password');

                $access = $this->accessModel->checkPassword($email, $password);

                if (empty($access)) {
                    // Login failed
                    $this->session->setFlashdata('error_message', lang('access_lang.msg_error_invalid_password'));

                } else {
                    // Login success, set session variables
                    $_SESSION['user_id'] = $access['person']['id'];
                    $_SESSION['access_id'] = $access['id'];
                    $_SESSION['user_email'] = $access['person']['email'];
                    $_SESSION['access_level'] = $access['access_level']['level'];
                    $_SESSION['logged_in'] = true;

                    // Redirect after login success
                    return redirect()->to($_SESSION['after_login_redirect']);
                }
            }

            //Display login page
            $output['title'] = lang('access_lang.title_login');
            if(!empty($_SESSION['error_message'])) {
                $output['error_message'] = $this->session->getFlashdata('error_message');
            }
            return $this->display_view('\Access\login', $output);

        } else {
            // If user is already logged in, redirect
            return redirect()->to($_SESSION['after_login_redirect']);
        }
    }

    /**
     * Logout and destroy session
     *
     * @return Response : Redirect to the base_url
     */
    public function logout(): Response
    {
        // Restart session with empty parameters
        $_SESSION = [];
        session_reset();
        session_unset();

        return redirect()->to(base_url());
    }

    /**
     * Let user change his own password
     *
     * @return string|Response : Display the form to let user change password or
     *                           redirect
     */
    public function change_my_password(): string|Response
    {
        // Check if access is allowed
        if($this->check_permission('@')) {

            // Get access rights from DB, destroy session if no access rights are found
            $access = $this->accessModel->find($_SESSION['access_id']);
            if (empty($access)) return redirect()->to('logout');

            // Empty errors message in output
            $output['errors'] = [];

            // Check if the form has been submitted, else just display the form
            if (!is_null($this->request->getVar('btn_change_password'))) {
                $old_password = $this->request->getVar('old_password');

                if($this->accessModel->checkPassword($access['person']['email'], $old_password)) {
                    $access['password'] = $this->request->getVar('new_password');
                    $access['password_confirm'] = $this->request->getVar('confirm_password');

                    $this->accessModel->update($access['id'], $access);

                    if ($this->accessModel->errors()==null) {
                        // No error happened, redirect
                        return redirect()->to(base_url());
                    } else {
                        // Display error messages
                        $output['errors'] = $this->accessModel->errors();
                    }

                } else {
                    // Old password error
                    $output['errors'][] = lang('access_lang.msg_error_invalid_old_password');
                }
            }

            // Display the password change form
            $output['title'] = lang('access_lang.title_change_my_password');
            return $this->display_view('\Access\change_my_password', $output);
        }
    }
} ?>
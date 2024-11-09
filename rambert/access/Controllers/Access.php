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
        $this->accessModel->find([1,2]);
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

                $user = $this->accessModel->check_password($email, $password);

                if (empty($user)) {
                    // Login failed
                    $this->session->setFlashdata('error_message', lang('access_lang.msg_error_invalid_password'));

                } else {
                    // Login success, set session variables
                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['user_email'] = $user->email;
                    $_SESSION['user_access'] = $user->access_level;
                    $_SESSION['logged_in'] = true;

                    // Redirect after login success
                    return redirect()->to($_SESSION['after_login_redirect']);
                }
            }

            //Display login page
            $output['title'] = lang('access_lang.title_login');
            if(!empty($_SESSION['error_message'])) {
                $output['error_message'] = $session->getFlashdata('error_message');
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
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {

            // Get user from DB, destroy session if user doesn't exist
            $user = $this->user_model->withDeleted()->find($_SESSION['user_id']);
            if (empty($user)) return redirect()->to('logout');

            // Empty errors message in output
            $output['errors'] = [];

            // Check if the form has been submitted, else just display the form
            if (!is_null($this->request->getVar('btn_change_password'))) {
                $old_password = $this->request->getVar('old_password');

                if($this->accessModel->check_password($user['email'], $old_password)) {
                    $user['password'] = $this->request->getVar('new_password');
                    $user['password_confirm'] = $this->request->getVar('confirm_password');

                    $this->accessModel->update($user['id'], $user);

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

        } else {
            // User is not logged in, redirect to login form
            return redirect()->to('login');
        }
    }
} ?>
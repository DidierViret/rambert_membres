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
use Psr\Log\LoggerInterface;

use Access\Models\Access_model;
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
        $this->access_model = new Access_model();
    }

    /**
     * Display login form.
     * Check login and password when submitted.
     * Redirect after login success.
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
        if(!(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true)) {

            // Check if the form has been submitted, else just display the form
            if (!is_null($this->request->getVar('btn_login'))) {

                $email = $this->request->getVar('email');
                $password = $this->request->getvar('password');

                $user = $this->check_password($email, $password);

                if (empty($user)) {
                    // Login failed
                    $this->session->setFlashdata('error_message', lang('user_lang.msg_err_invalid_password'));

                } else {
                    // Login success, set session variables
                    $this->session->set('user_id', $user->id);
                    $this->session->set('username', $user->username);
                    $this->session->set('user_access', $user->access_level);
                    $this->session->set('logged_in', true);

                    // Redirect after login success
                    return redirect()->to($_SESSION['after_login_redirect']);

                }
            }

            //Display login page
            $output['title'] = lang('user_lang.title_page_login');
            if(!is_null($this->session->getFlashdata('message-danger'))) {
                $output['error_message'] = $session->getFlashdata('error_message');
            }
            return $this->display_view('\Access\login', $output);

        } else {
            // If user is already logged in, redirect
            return redirect()->to($_SESSION['after_login_redirect']);
        }
    }
} ?>
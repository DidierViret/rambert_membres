<?php
/**
 * Routes for access module
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */

$routes->get('login','\Access\Controllers\Access::login');
$routes->post('login','\Access\Controllers\Access::login');
$routes->get('logout','\Access\Controllers\Access::logout');
$routes->get('change_my_password','\Access\Controllers\Access::change_my_password');
$routes->post('change_my_password','\Access\Controllers\Access::change_my_password');

$routes->get('test','\Access\Controllers\Access::test');
?>
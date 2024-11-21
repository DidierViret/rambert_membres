<?php
/**
 * Routes for access module
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */

// All users routes
$routes->get('login','\Access\Controllers\Access::login');
$routes->post('login','\Access\Controllers\Access::login');
$routes->get('logout','\Access\Controllers\Access::logout');
$routes->get('change_my_password','\Access\Controllers\Access::change_my_password');
$routes->post('change_my_password','\Access\Controllers\Access::change_my_password');

// Admin routes
$routes->get('access', '\Access\Controllers\Admin::list');
$routes->get('access/create', '\Access\Controllers\Admin::create');
$routes->post('access/create/(:num)', '\Access\Controllers\Admin::create/$1');
$routes->get('access/update/(:num)', '\Access\Controllers\Admin::update/$1');
$routes->post('access/update/(:num)', '\Access\Controllers\Admin::update/$1');
$routes->get('access/delete/(:num)', '\Access\Controllers\Admin::delete');
$routes->get('access/restore/(:num)', '\Access\Controllers\Admin::restore');


$routes->get('test','\Access\Controllers\Access::test');
$routes->get('testadmin','\Access\Controllers\Admin::test');
?>
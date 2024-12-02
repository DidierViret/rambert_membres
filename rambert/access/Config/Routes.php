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
$routes->get('access', '\Access\Controllers\Admin::listAccess');
$routes->get('access/create', '\Access\Controllers\Admin::createAccess');
$routes->post('access/save', '\Access\Controllers\Admin::saveAccess');
$routes->get('access/update/(:num)', '\Access\Controllers\Admin::updateAccess/$1');
$routes->post('access/update/(:num)', '\Access\Controllers\Admin::updateAccess/$1');
$routes->get('access/delete/(:num)', '\Access\Controllers\Admin::deleteAccess/$1');
$routes->get('access/restore/(:num)', '\Access\Controllers\Admin::restoreAccess/$1');


$routes->get('test','\Access\Controllers\Access::test');
$routes->get('testadmin','\Access\Controllers\Admin::test');
?>
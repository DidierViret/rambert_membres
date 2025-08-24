<?php
/**
 * Routes for members module
 *
 * @author      Club Rambert, Didier Viret
 * @link        https://rambert.ch
 * @copyright   Copyright (c), club Rambert
 */

// Individual members routes
$routes->get('members', '\Members\Controllers\Members::membersList');
$routes->get('person/update/(:num)', '\Members\Controllers\MembersAdmin::personUpdate/$1');
$routes->post('person/save/(:num)', '\Members\Controllers\MembersAdmin::personSave/$1');

// Home routes
$routes->get('home/(:num)', '\Members\Controllers\Members::homeDetails/$1');
$routes->get('home/update/(:num)', '\Members\Controllers\MembersAdmin::homeUpdate/$1');
$routes->post('home/save/(:num)', '\Members\Controllers\MembersAdmin::homeSave/$1');

// Contributions routes
$routes->get('contributions/(:num)', '\Members\Controllers\MembersAdmin::contributionsList/$1');
$routes->get('contribution/update/(:num)', '\Members\Controllers\MembersAdmin::contributionUpdate/$1');
$routes->get('contribution/delete/(:num)', '\Members\Controllers\MembersAdmin::contributionDelete/$1');
$routes->get('contribution/create', '\Members\Controllers\MembersAdmin::contributionCreate/$1');
$routes->post('contribution/save/(:num)', '\Members\Controllers\MembersAdmin::contributionSave/$1');
?>
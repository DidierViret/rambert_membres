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

// Home routes
$routes->get('home/(:num)', '\Members\Controllers\Members::homeDetails/$1');
$routes->get('home/update/(:num)', '\Members\Controllers\MembersAdmin::homeUpdate/$1');
$routes->post('home/save/(:num)', '\Members\Controllers\MembersAdmin::homeSave/$1');
?>
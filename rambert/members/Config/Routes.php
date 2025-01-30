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
?>
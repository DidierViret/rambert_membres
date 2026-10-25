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
$routes->get('person/create/(:num)', '\Members\Controllers\MembersAdmin::personCreate/$1');
$routes->post('person/save/(:num)', '\Members\Controllers\MembersAdmin::personSave/$1');
$routes->get('person/delete/(:num)', '\Members\Controllers\MembersAdmin::personConfirmDelete/$1');
$routes->post('person/delete/(:num)', '\Members\Controllers\MembersAdmin::personDelete/$1');
$routes->get('person/restore/(:num)', '\Members\Controllers\MembersAdmin::personConfirmRestore/$1');
$routes->post('person/restore/(:num)', '\Members\Controllers\MembersAdmin::personRestore/$1');

// Home routes
$routes->get('home/(:num)', '\Members\Controllers\Members::homeDetails/$1');
$routes->get('home/update/(:num)', '\Members\Controllers\MembersAdmin::homeUpdate/$1');
$routes->get('home/create', '\Members\Controllers\MembersAdmin::homeCreate');
$routes->post('home/save/(:num)', '\Members\Controllers\MembersAdmin::homeSave/$1');

// Contributions routes
$routes->get('contributions/(:num)', '\Members\Controllers\MembersAdmin::contributionsList/$1');
$routes->get('contribution/update/(:num)', '\Members\Controllers\MembersAdmin::contributionUpdate/$1');
$routes->get('contribution/delete/(:num)', '\Members\Controllers\MembersAdmin::contributionConfirmDelete/$1');
$routes->post('contribution/delete/(:num)', '\Members\Controllers\MembersAdmin::contributionDelete/$1');
$routes->get('contribution/create/(:num)', '\Members\Controllers\MembersAdmin::contributionCreate/$1');
$routes->post('contribution/save/(:num)', '\Members\Controllers\MembersAdmin::contributionSave/$1');

// Lists routes
$routes->get('lists', '\Members\Controllers\Lists::index');
$routes->get('lists/postal-send', '\Members\Controllers\Lists::listPostalSend');
$routes->get('lists/export-excel', '\Members\Controllers\Lists::exportExcel');

// Changes routes
$routes->get('changes', '\Members\Controllers\MembersAdmin::changesList');

// Teams and roles routes
$routes->get('teams', '\Members\Controllers\TeamsAdmin::teamsAdmin');
$routes->get('teams/(:num)', '\Members\Controllers\TeamsAdmin::teamsAdmin/$1');
$routes->get('teams/create', '\Members\Controllers\TeamsAdmin::teamCreate');
$routes->get('teams/update/(:num)', '\Members\Controllers\TeamsAdmin::teamUpdate/$1');
$routes->post('teams/save/(:num)', '\Members\Controllers\TeamsAdmin::teamSave/$1');
$routes->get('teams/delete/(:num)', '\Members\Controllers\TeamsAdmin::teamConfirmDelete/$1');
$routes->post('teams/delete/(:num)', '\Members\Controllers\TeamsAdmin::teamDelete/$1');
$routes->get('teams/restore/(:num)', '\Members\Controllers\TeamsAdmin::teamConfirmRestore/$1');
$routes->post('teams/restore/(:num)', '\Members\Controllers\TeamsAdmin::teamRestore/$1');
$routes->get('teams/role/create/(:num)', '\Members\Controllers\TeamsAdmin::roleCreate/$1');
$routes->get('teams/role/update/(:num)', '\Members\Controllers\TeamsAdmin::roleUpdate/$1');
$routes->post('teams/role/save/(:num)', '\Members\Controllers\TeamsAdmin::roleSave/$1');
$routes->get('teams/role/delete/(:num)', '\Members\Controllers\TeamsAdmin::roleConfirmDelete/$1');
$routes->post('teams/role/delete/(:num)', '\Members\Controllers\TeamsAdmin::roleDelete/$1');
$routes->get('teams/role/restore/(:num)', '\Members\Controllers\TeamsAdmin::roleConfirmRestore/$1');
$routes->post('teams/role/restore/(:num)', '\Members\Controllers\TeamsAdmin::roleRestore/$1');
?>
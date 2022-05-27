<?php
/*
Complete Documentation on: https://github.com/nikic/FastRoute
Example Routes:
$routes->get('/urlpath[/{optionalparameter}]', 'Controller@Action');
$routes->post('/article/{id:\d+}', 'Controller@Action'); With ID-Parameter (Numeric)
*/


$routes->get('/', 'Redirect@to_main_page');
$routes->get('/cms', 'CMS@cms');
$routes->get('/cms/stats', 'CMS@stats');
$routes->get('/cms/category/{category}', 'CMS@cms');
$routes->get('/redirect/create', 'CMS@create');
$routes->post('/redirect/create', 'CMS@save');
$routes->get('/redirect/{id:\d+}', 'CMS@edit');
$routes->get('/redirect/{id:\d+}/remove', 'CMS@delete');
$routes->post('/redirect/{id:\d+}', 'CMS@update');
$routes->get('/search', 'CMS@search');

// Authentication Routes
$routes->get('/login', 'Authentication@login');
$routes->post('/login', 'Authentication@login');
$routes->get('/logout', 'Authentication@logout');
$routes->get('/profile', 'Authentication@profile');
$routes->get('/password-reset', 'Authentication@password_reset_form');
$routes->post('/password-reset', 'Authentication@password_reset_send_mail');
$routes->get('/password-change[/{resetToken}]', 'Authentication@password_change_form');
$routes->post('/password-change[/{resetToken}]', 'Authentication@password_change_process');
$routes->get('/profile/edit', 'Authentication@edit_profile');
$routes->post('/profile/edit', 'Authentication@edit_profile');

// Usermanagement / Admin Routes
/*
$routes->get('/admin', 'Usermanagement@index');
$routes->get('/admin/new', 'Usermanagement@new');
$routes->post('/admin', 'Usermanagement@create');
$routes->get('/admin/{id:\d+}', 'Usermanagement@show');
$routes->get('/admin/{id:\d+}/delete/{token}', 'Usermanagement@delete');
$routes->post('/admin/{id:\d+}', 'Usermanagement@update');
*/

// Generall Redirect Capture All
$routes->get('/{shortURL}', 'Redirect@find');

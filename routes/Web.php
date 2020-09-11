<?php
require '../vendor/autoload.php';

use Source\Core\Router;

$router = new Router();

// WEB
$router->namespace(null)->group(null);
$router->get('/', 'Web@index');

// ADMIN
$router->namespace('Admin')->group('/admin');
$router->get('/dashboard', 'Dash@index');
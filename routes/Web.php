<?php
require '../vendor/autoload.php';

use Source\Core\Router;

$router = new Router();

// WEB
$router->namespace(null)->group(null);
$router->get('/', 'Web@index');

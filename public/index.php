<?php 

session_start();
require '../vendor/autoload.php';
require '../routes/Web.php';

$router->run($router->routes);
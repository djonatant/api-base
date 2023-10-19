<?php
require_once 'vendor/autoload.php';

use Entersis\Database;
use Entersis\Router;
\Entersis\Headers::setCORSHeaders(
    $_ENV['API_URL'],
    "GET, POST, PUT, DELETE",
    "Content-Type, Authorization"
);

$db = new Database();
$router = new Router($db);

//Api
$router->addRoute('GET', '/', 'Entersis\Endpoint\Controller\HealthController@get');
$router->addRoute('POST', '/auth/v1/sign-up', 'Entersis\Endpoint\Controller\SignupController@post');
$router->addRoute('POST', '/auth/v1/login', 'Entersis\Endpoint\Controller\LoginController@post');

$router->run();

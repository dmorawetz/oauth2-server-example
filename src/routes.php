<?php

use Controllers\HomeController;
use Controllers\HelloController;
use Controllers\LoginController;
use Controllers\AuthorizationController;

use Middleware\Authentication;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

$app->get('/', HomeController::class . ':home');

$app->map(['GET', 'POST'], '/login', LoginController::class . ':login')->setName('login');

$app->map(['GET', 'POST'], '/authorize', AuthorizationController::class . ':authorize')->add(Authentication::class);
$app->post('/access_token', AuthorizationController::class . ':token');

$app->get('/hello/{name}', HelloController::class . ':greet')->add(Authentication::class);
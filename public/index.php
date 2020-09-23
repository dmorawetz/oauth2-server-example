<?php

use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

session_start();

require __DIR__ . '/../src/dependencies.php';

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, false, false);
$app->addMiddleware(
    TwigMiddleware::create($app,$container->get(Twig::class))
);

require __DIR__ . '/../src/routes.php';

$app->run();

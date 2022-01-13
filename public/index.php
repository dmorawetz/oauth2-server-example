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

function load_php_files($directory) {
    if(is_dir($directory)) {
        $scan = scandir($directory);
        unset($scan[0], $scan[1]); //unset . and ..
        foreach($scan as $file) {
            if(is_dir($directory."/".$file)) {
                load_php_files($directory."/".$file);
            } else {
                if(strpos($file, '.php') !== false) {
                    include_once($directory."/".$file);
                }
            }
        }
    }
}

load_php_files(__DIR__ . '/../src');

$app->run();

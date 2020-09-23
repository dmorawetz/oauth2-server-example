<?php

namespace Controllers;

use Psr\Container\ContainerInterface;
use Slim\Views\Twig;

class HelloController
{
    protected $container;
   
    // constructor receives container instance
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
   
    public function greet($request, $response, $args) {
        $name = $args['name'];
        $response->getBody()->write("Hello, $name");
        return $response;
    }
}  

<?php

namespace Controllers;

use Psr\Container\ContainerInterface;
use Slim\Views\Twig;

class HomeController
{
    protected $container;
   
    // constructor receives container instance
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
   
    public function home($request, $response, $args) {
         return $this->container->get(Twig::class)->render($response, "home.twig", [
             'pageTitle' => 'Home',
         ]);
    }
}  

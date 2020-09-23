<?php

namespace Controllers;

use Psr\Container\ContainerInterface;
use Slim\Views\Twig;

class LoginController
{
    protected $container;
   
    // constructor receives container instance
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
   
    public function login($request, $response, $args) {
        if ($request->getMethod() == 'GET') {
            return $this->container->get(Twig::class)->render($response, "login.twig");
        }

        // TODO your login logic
        $loginSuccessful = true;
        if($loginSuccessful) {
            session_start();
            $_SESSION['user_id'] = 1;
        }
            
        return $this->container->get(Twig::class)->render($response, "login.twig", [
            'errorMessage' => 'Email or password wrong!'
        ]);
    }
}

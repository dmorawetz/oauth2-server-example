<?php

namespace Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Container\ContainerInterface;

use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

class Authentication {

    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function __invoke(Request $request,
                             RequestHandler $handler)
    {
        $loggedIn = $_SESSION['isLoggedIn'];
        if ($loggedIn != 'yes') {
            $routeContext = RouteContext::fromRequest($request);
            $routeParser = $routeContext->getRouteParser();
            $currentRoute = $routeContext->getRoute();
            $url = $routeParser->urlFor(
                      'login',
                      [], 
                      ['redirect_url' =>
                          $_SERVER['REQUEST_URI']]);
            
            $response = new Response();
            // If the user is not logged in, redirect them to login
            return $response->withHeader('Location', $url)
                            ->withStatus(302);
        }

        // The user must be logged in, so pass this request
        // down the middleware chain
        $response = $handler->handle($request);

        // And pass the request back up the middleware chain.
        return $response;
    }
}
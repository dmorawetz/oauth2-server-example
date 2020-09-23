<?php

namespace Controllers;

use League\OAuth2\Server\AuthorizationServer;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Slim\Psr7\Stream;

use Entities\UserEntity;

class AuthorizationController
{
    protected $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function authorize($request, $response, $args) {
        $server = $this->container->get(AuthorizationServer::class);
        
        try {
            // Validate the HTTP request and return an AuthorizationRequest object.
            // The auth request object can be serialized into a user's session
            $authRequest = $server->validateAuthorizationRequest($request);

            $user = new UserEntity();
            $user->setIdentifier($_SESSION['user_id']);
            $authRequest->setUser($user);


            if ($request->getMethod() == 'GET') {
                $queryParams = $request->getQueryParams();
                $scopes = isset($queryParams['scope']) ? explode(" ", $queryParams['scope']) : ['default'];

                return $this->container->get(Twig::class)->render($response, "authorize.twig", [
                    'pageTitle' => 'Authorize',
                    'clientName' => $authRequest->getClient()->getName(),
                    'scopes' => $scopes
                ]);
            }

            $params = (array)$request->getParsedBody();

            // Once the user has approved or denied the client update the status
            // (true = approved, false = denied)
            $authorized = $params['authorized'] == 'true';
            $authRequest->setAuthorizationApproved($authorized);

            // Return the HTTP redirect response
            return $server->completeAuthorizationRequest($authRequest, $response);

        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);

        } catch (\Exception $exception) {
            $body = $response->getBody();
            $body->write($exception->getMessage());

            return $response->withStatus(500)->withBody($body);
        }
    }

    public function token($request, $response, $args) {
        $server = $this->container->get(AuthorizationServer::class);

        try {

            // Try to respond to the access token request
            return $server->respondToAccessTokenRequest($request, $response);

        } catch (OAuthServerException $exception) {

            // All instances of OAuthServerException can be converted to a PSR-7 response
            return $exception->generateHttpResponse($response);

        } catch (\Exception $exception) {

            // Catch unexpected exceptions
            $body = $response->getBody();
            $body->write($exception->getMessage());
            return $response->withStatus(500)->withBody($body);

        }
    }
}

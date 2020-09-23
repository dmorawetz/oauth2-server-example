<?php

use Repositories\AccessTokenRepository;
use Repositories\AuthCodeRepository;
use Repositories\ClientRepository;
use Repositories\RefreshTokenRepository;
use Repositories\ScopeRepository;

use Defuse\Crypto\Key;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;

use Psr\Container\ContainerInterface;

use DI\ContainerBuilder;

use Slim\Views\Twig;

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    Twig::class => function (ContainerInterface $container): Twig {
        // Instantiate twig.
        return Twig::create(
            __DIR__ . '/../src/Templates'
        );
    },
    
]);

$container = $containerBuilder->build();

$container->set(AuthorizationServer::class,
    function (ContainerInterface $container) {
        include 'config/encryption-key.php';

        // Setup the authorization server
        $server = new AuthorizationServer(
            // instance of ClientRepositoryInterface
            new ClientRepository(),
            // instance of AccessTokenRepositoryInterface              
            new AccessTokenRepository(),
            // instance of ScopeRepositoryInterface            
            new ScopeRepository(),            
            // path to private key      
            'file://'.__DIR__.'/../src/config/private.key',
            // encryption key   
            Key::loadFromAsciiSafeString($encryptionKey)     
        );

        $refreshTokenRepository = new RefreshTokenRepository();
        $grant = new AuthCodeGrant(
            new AuthCodeRepository(),
            // instance of RefreshTokenRepositoryInterface
            $refreshTokenRepository,   
            new DateInterval('PT10M')
        );

        // Enable the password grant on the server
        // with a token TTL of 1 hour
        $server->enableGrantType(
            $grant,
            // access tokens will expire after 1 hour           
            new DateInterval('PT1H') 
        );

        $rt_grant = new RefreshTokenGrant($refreshTokenRepository);
        // new refresh tokens will expire after 1 month        
        $rt_grant->setRefreshTokenTTL(new DateInterval('P1M')); 

        // Enable the refresh token grant on the server
        $server->enableGrantType(
            $rt_grant,
            // new access tokens will expire after an hour
            new DateInterval('PT1H') 
        );

        return $server;
    }
);
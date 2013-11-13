<?php

namespace Igorw\Stack\OAuth;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\StreamClient;
use OAuth\Common\Storage\SymfonySession;
use OAuth\OAuth1\Signature\Signature;
use OAuth\OAuth1\Service\Twitter;
use Pimple;
use Igorw\Stack\Router;

class ContainerConfig
{
    public function process(Pimple $container)
    {
        $container['storage'] = $container->share(function ($container) {
            return new SymfonySession($container['session']);
        });

        $container['credentials'] = $container->share(function ($container) {
            return new Credentials(
                $container['key'],
                $container['secret'],
                $container['callback_url']
            );
        });

        $container['signature'] = $container->share(function ($container) {
            return new Signature($container['credentials']);
        });

        $container['http_client'] = $container->share(function () {
            return new StreamClient();
        });

        $container['oauth_service.class'] = 'OAuth\OAuth1\Service\Twitter';

        $container['oauth_service'] = $container->share(function ($container) {
            return new $container['oauth_service.class'](
                $container['credentials'],
                $container['http_client'],
                $container['storage'],
                $container['signature']
            );
        });

        $container['auth_controller'] = $container->share(function ($container) {
            return new AuthController(
                $container['storage'],
                $container['oauth_service'],
                $container['success_url'],
                $container['failure_url']
            );
        });

        $container['routes'] = [
            '/auth'         => 'auth_controller:authAction',
            '/auth/verify'  => 'auth_controller:verifyAction',
        ];

        $container['router'] = $container->share(function ($container) {
            return new Router($container, $container['routes']);
        });

        $container['success_url'] = null;

        $container['token_translator'] = $container->protect(function ($token) {
            return $token;
        });
    }
}

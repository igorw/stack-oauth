<?php

namespace Stack;

use OAuth\Common\Service\AbstractService;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Token\AbstractToken;
use Pimple;
use Stack\OAuth\AuthController;
use Symfony\Component\HttpFoundation\Request;

class RouterTest extends \PHPUnit_Framework_TestCase
{
//    public function match(Request $request)
//    {
//        foreach ($this->map as $path => $definition) {
//            if ($path === $request->getPathInfo()) {
//                return $this->getController($definition);
//            }
//        }
//
//        return null;
//    }

    /**
     * @test
     */
    public function it_gets_the_controller()
    {
        $container = new Pimple();

        // @todo mock these guys
        $container['storage'] = $this->getMock('Stack\TokenStorage');
        $container['oauth_service'] = $this->getMock('Stack\ServiceInterface');
        $container['success_url'] = 'http://localhost:8080/success';
        $container['failure_url'] = 'http://localhost:8080/failure';

        $container['auth_controller'] = $container->share(function ($container) {
            return new AuthController(
                $container['storage'],
                $container['oauth_service'],
                $container['success_url'],
                $container['failure_url']
            );
        });

        // path => definition
        $map = [
            '/auth1' => 'auth_controller:actionA',
            '/auth2' => 'auth_controller:actionB',
        ];

        $router = new Router($container, $map);

        list($controller, $action) = $router->getController($map['/auth1']);
        $this->assertInstanceOf('Stack\OAuth\AuthController', $controller);
        $this->assertEquals('actionA', $action);

        list($controller, $action) = $router->getController($map['/auth2']);
        $this->assertInstanceOf('Stack\OAuth\AuthController', $controller);
        $this->assertEquals('actionB', $action);
    }
}

class TokenStorage extends AbstractToken
{
}

class OauthService extends AbstractService
{
    public function getAuthorizationEndpoint()
    {
    }

    public function getAccessTokenEndpoint()
    {
    }

    public function request($path, $method = 'GET', array $body = [], array $extraHeaders = [])
    {
    }

    public function getAuthorizationUri( array $additionalParameters = [] )
    {
    }
}

<?php

namespace Stack;

use OAuth\Common\Service\AbstractService;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Token\AbstractToken;
use OAuth\Common\Token\TokenInterface;
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

        $container['storage'] = $this->getMock('Stack\TokenStorage');
        $container['oauth_service'] = $this->getMockBuilder('OAuth\OAuth1\Service\Twitter')
            ->disableOriginalConstructor()
            ->getMock()
        ;
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

class TokenStorage implements TokenStorageInterface
{
    public function storeAccessToken(TokenInterface $token)
    {
    }

    public function retrieveAccessToken()
    {
    }
}

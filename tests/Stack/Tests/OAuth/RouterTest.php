<?php

namespace Stack;

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
        $container['storage'] = $this->getMock('TokenStorageInterface');
        $container['oauth_service'] = $this->getMock('ServiceInterface');
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

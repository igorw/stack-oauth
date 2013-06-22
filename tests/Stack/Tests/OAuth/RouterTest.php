<?php

namespace Stack;

use Pimple;
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

        // path => definition
        $map = [
            '/auth1' => 'oauth:actionA',
            '/auth2' => 'oauth:actionB',
        ];

        $router = new Router($container, $map);

        list($controllerOne, $action) = $router->getController($map['/auth1']);
        $this->assertInstanceOf('Stack\OAuth\AuthController', $controllerOne);
        list($controllerTwo, $action) = $router->getController($map['/auth2']);
        $this->assertInstanceOf('Stack\OAuth\AuthController', $controllerTwo);
    }
}

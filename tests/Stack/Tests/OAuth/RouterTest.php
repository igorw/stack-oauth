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
     * @dataProvider getActions
     */
    public function getController($expectedAction)
    {
        $container = new Pimple();
        $definition = [
            'className:actionA',
            'className:actionB',
            'className:actionC',
        ];


        $map = array();
        $router = new Router($container, $map);
        list($controller, $action) = $router->getController($definition);

        $this->assertEqual($expectedAction, $action);
    }

    public function getActions()
    {
        return [
            ['actionA'],
            ['actionB'],
            ['actionC']
        ];
    }
}

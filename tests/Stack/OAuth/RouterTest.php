<?php

namespace Stack;

use Pimple;
use Symfony\Component\HttpFoundation\Request;

class RouterTest
{
    public function setUp()
    {
        $this->container = $container;
        $this->map = $map;
    }

    public function match(Request $request)
    {
        foreach ($this->map as $path => $definition) {
            if ($path === $request->getPathInfo()) {
                return $this->getController($definition);
            }
        }

        return null;
    }

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

        $router = new Router();
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

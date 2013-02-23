<?php

namespace Stack;

use Pimple;
use Symfony\Component\HttpFoundation\Request;

class Router
{
    private $container;
    private $map;

    public function __construct(Pimple $container, array $map)
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

    private function getController($definition)
    {
        list($service, $action) = explode(':', $definition);
        $controller = $this->container[$service];

        return [$controller, $action];
    }
}

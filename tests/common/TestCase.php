<?php

namespace common;

use Pimple;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\HttpKernel\HttpKernelInterface;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $mockFileSessionStorage;

    protected function setUp()
    {
        $this->mockFileSessionStorage = new MockFileSessionStorage();
    }

//    protected function sessionify(HttpKernelInterface $app, array $config = [])
//    {
//        $config = array_merge([
//            'session.storage' => Pimple::share(function () {
//                return $this->mockFileSessionStorage;
//            }),
//        ], $config);
//
//        return new Session($app, $config);
//    }
}
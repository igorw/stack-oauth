<?php

namespace Stack\Tests;

use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Token\TokenInterface;
use Pimple;
use Stack\OAuth\AuthController;
use Stack\OAuth;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OAuthTest extends \PHPUnit_Framework_TestCase
{
    protected $container;
    protected $map;

    public function setUp()
    {

    }

    /** @test */
    public function it_automatically_sets_its_container_configuration()
    {
        $controllerMock = $this->getMockBuilder('Stack\OAuth\AuthController')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $controllerMock
            ->expects($this->any())
            ->method('authAction')
            ->with($this->isInstanceOf('Symfony\Component\HttpFoundation\Request'))
            ->will($this->returnValue(Response::create('ok')))
        ;

        $app = $this->getHttpKernelMock(new Response('ok'));
        $oauthApp = new OAuth($app, ['auth_controller' => $controllerMock]);
        $response = $oauthApp->handle(Request::create('/auth'));
        $this->assertContains('ok', $response->getContent());
    }

    private function getHttpKernelMock(Response $response)
    {
        $app = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $app->expects($this->any())
            ->method('handle')
            ->with($this->isInstanceOf('Symfony\Component\HttpFoundation\Request'))
            ->will($this->returnValue($response))
        ;

        return $app;
    }
}

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
    public function setUp()
    {

    }

    /** @test */
    public function handlesSpecificAuthRequest()
    {
        $app = $this->getHttpKernelMock(Response::create('ok'));
        $oauthApp = new OAuth($app, ['auth_controller' => $this->getControllerMock()]);
        $response = $oauthApp->handle(Request::create('/auth'));
        $this->assertContains('ok', $response->getContent());
    }

    /** @test */
    public function loadsMiddlewareTokenInRequest()
    {
        $app = $this->getHttpKernelMock(Response::create('ok'));
        $oauthApp = new OAuth($app, []);
        $response = $oauthApp->handle(Request::create('/one_path'));
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

    private function getControllerMock()
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

        return $controllerMock;
    }
}

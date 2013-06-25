<?php

namespace Stack;

use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Token\TokenInterface;
use Pimple;
use Stack\OAuth\AuthController;
use Stack\OAuth;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

class OAuthTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function handlesSpecificAuthRequest()
    {
        $app = $this->getHttpKernelMock(Response::create('ok'));
        $oauthApp = new OAuth($app, ['auth_controller' => $this->getControllerMock()]);
        $requestWithSession = Request::create('/auth');
        $requestWithSession->setSession(new Session());
        $response = $oauthApp->handle($requestWithSession);
        $this->assertContains('ok', $response->getContent());
    }

    /** @test */
    public function loadsTokenInRequest()
    {
        $app = $this->getHttpKernelMock(Response::create('ok'));
        $oauthApp = new OAuth($app, []);
        $requestWithSession = Request::create('/one_path');
        $attributes = new AttributeBag('_auth_attributes');
        $attributes->setName('auth_attribute_name');
        $mockFileSessionStorage = new MockFileSessionStorage();
        $session = new Session($mockFileSessionStorage, $attributes);
        $session->start();
        $session->set('lusitanian_oauth_token', 'token123');
        $requestWithSession->setSession($session);
        $response = $oauthApp->handle($requestWithSession);

        $this->assertEquals('token123', $requestWithSession->get('oauth.token'));
        $this->assertContains('ok', $response->getContent());

        $session->remove('lusitanian_oauth_token');
        $requestWithSession->setSession($session);
        $response = $oauthApp->handle($requestWithSession);

        $this->assertNull($requestWithSession->get('oauth.token'));
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

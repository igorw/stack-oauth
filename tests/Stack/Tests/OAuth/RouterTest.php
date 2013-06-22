<?php

namespace Stack;

use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Token\TokenInterface;
use Pimple;
use Stack\OAuth\AuthController;
use Symfony\Component\HttpFoundation\Request;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    protected $container;
    protected $map;

    public function setUp()
    {
        $this->container = new Pimple();

        $this->container['storage'] = $this->getMock('Stack\TokenStorage');
        $this->container['oauth_service'] = $this->getMockBuilder('OAuth\OAuth1\Service\Twitter')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->container['success_url'] = 'http://localhost:8080/success';
        $this->container['failure_url'] = 'http://localhost:8080/failure';

        $this->container['auth_controller'] = $this->container->share(function ($container) {
            return new AuthController(
                $container['storage'],
                $container['oauth_service'],
                $container['success_url'],
                $container['failure_url']
            );
        });

        $this->map = [
            '/auth1' => 'auth_controller:actionA',
            '/auth2' => 'auth_controller:actionB',
        ];
    }

    /**
     * @test
     */
    public function it_matches_when_request_pathinfo_path_is_mapped()
    {
        $router = new Router($this->container, $this->map);
        $request = Request::create('/auth1');
        list($controller, $action) = $router->match($request);
        $this->assertInstanceOf('Stack\OAuth\AuthController', $controller);
        $this->assertEquals('actionA', $action);
    }

    /**
     * @test
     */
    public function it_does_not_match_for_non_mapped_path()
    {
        $router = new Router($this->container, $this->map);
        $request = Request::create('/something_else_not_mapped');
        list($controller, $action) = $router->match($request);
        $this->assertNull($controller);
        $this->assertNull($action);
    }

    /**
     * @test
     */
    public function it_gets_the_controller()
    {
        $router = new Router($this->container, $this->map);

        list($controller, $action) = $router->getController($this->map['/auth1']);
        $this->assertInstanceOf('Stack\OAuth\AuthController', $controller);
        $this->assertEquals('actionA', $action);

        list($controller, $action) = $router->getController($this->map['/auth2']);
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

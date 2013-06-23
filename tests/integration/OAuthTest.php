<?php

namespace integration;

use common\TestCase;
use Pimple;
use Stack\CallableHttpKernel;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class OAuthTest extends TestCase
{
    const SIMULATED_TIME = 1337882841;

    public function testDefaultSetsNoCookies()
    {
        $app = new CallableHttpKernel(function (Request $request) {
            return new Response('test');
        });

        $client = new Client($app);

        $client->request('GET', '/');

        $this->assertEquals('test', $client->getResponse()->getContent());

        $cookies = $client->getResponse()->headers->getCookies();
        $this->assertCount(0, $cookies);
    }
}
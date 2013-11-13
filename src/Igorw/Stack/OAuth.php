<?php

namespace Igorw\Stack;

use OAuth\Common\Storage\Exception\TokenNotFoundException;
use Pimple;
use Igorw\Stack\OAuth\ContainerConfig;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

class OAuth implements HttpKernelInterface
{
    private $app;
    private $container;

    public function __construct(HttpKernelInterface $app, array $options = [])
    {
        $this->app = $app;
        $this->container = $this->setupContainer($options);
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $this->container['session'] = $request->getSession();

        $controller = $this->container['router']->match($request);
        if ($controller) {
            return $controller($request);
        }

        try {
            $token = $this->container['storage']->retrieveAccessToken();
        } catch (TokenNotFoundException $e) {
            $token = null;
        }

        $request->attributes->set('oauth.token', $token);
        $request->attributes->set(
            'stack.authn.token',
            $this->container['token_translator']($token)
        );

        return $this->app->handle($request, $type, $catch);
    }

    private function setupContainer(array $options)
    {
        $container = new Pimple();

        $config = new ContainerConfig();
        $config->process($container);

        foreach ($options as $name => $value) {
            if (in_array($name, ['token_translator'])) {
                $container[$name] = $container->share(function () use ($value) {
                    return $value;
                });
            } else {
                $container[$name] = $value;
            }
        }

        return $container;
    }
}

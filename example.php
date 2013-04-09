<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

require 'vendor/autoload.php';

$app = new Stack\CallableHttpKernel(function ($request) {
    $token = $request->attributes->get('oauth.token');

    if (!$token) {
        return new RedirectResponse('/auth');
    }

    $params = $token->getExtraParams();
    return sprintf('Welcome @%s!', $params['screen_name']);
});

$stack = (new Stack\Builder())
    ->push('Stack\Session')
    ->push('Stack\OAuth', [
        'key'               => 'foo',
        'secret'            => 'bar',
        'callback_url'      => 'http://localhost:8080/auth/verify',
        'success_url'       => '/',
        'failure_url'       => '/auth',
    ]);

$app = $stack->resolve($app);

$request = Request::createFromGlobals();
$response = $app->handle($request)->send();
$app->terminate($request, $response);

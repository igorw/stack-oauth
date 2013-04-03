# Stack/OAuth

OAuth stack middleware.

## Usage

    new OAuth($app, [
        'key' => 'foo',
        'secret' => 'bar',
        'callback_url' => 'http://localhost:8080/auth/verify',
        'success_url' => '/',
        'failure_url' => '/auth',
    ]);

## Pre-defined URLs

* /auth
* /auth/verify

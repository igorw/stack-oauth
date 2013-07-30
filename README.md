# Stack/OAuth

OAuth stack middleware.

## Requirements

* **session**: The request must have session handling accounted for. You can
  do this be prepending the `stack/session` middleware to this one.

* **credentials**: You need to have some sort of OAuth server. By default,
  `stack/oauth` will use twitter. But you can change that through the
  `oauth_service.class` config parameter.

## Usage

    use Igorw\Stack\OAuth;

    $app = new OAuth($app, [
        'key'           => 'foo',
        'secret'        => 'bar',
        'callback_url'  => 'http://localhost:8080/auth/verify',
        'success_url'   => '/',
        'failure_url'   => '/auth',
    ]);

## Pre-defined URLs

* /auth
* /auth/verify

## TODO

* config validation
* tests
* more flexible path config (?)

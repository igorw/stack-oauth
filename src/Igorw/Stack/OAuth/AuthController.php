<?php

namespace Igorw\Stack\OAuth;

use OAuth\Common\Service\ServiceInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\OAuth1;
use OAuth\OAuth2;
use Symfony\Component\HttpFoundation;

class AuthController
{
    private $storage;
    private $oauth;
    private $successUrl;
    private $failureUrl;

    public function __construct(TokenStorageInterface $storage, ServiceInterface $oauth, $successUrl, $failureUrl)
    {
        $this->storage = $storage;
        $this->oauth = $oauth;
        $this->successUrl = $successUrl;
        $this->failureUrl = $failureUrl;

        if (!($this->oauth instanceof OAuth1\Service\ServiceInterface) && !($this->oauth instanceof OAuth2\Service\ServiceInterface)) {
            throw new \DomainException("Unknown implementation.");
        }
    }

    public function authAction(HttpFoundation\Request $request)
    {
        if ($this->oauth instanceof OAuth1\Service\ServiceInterface) {
            $token = $this->oauth->requestRequestToken();
            $url = $this->oauth->getAuthorizationUri(['oauth_token' => $token->getRequestToken()]);
        } elseif ($this->oauth instanceof OAuth2\Service\ServiceInterface) {
            $url = $this->oauth->getAuthorizationUri();
        }

        $request->getSession()->set('oauth.success_url', $request->headers->get('Referer'));

        return new HttpFoundation\RedirectResponse($url->getAbsoluteUri(), 302, ['Cache-Control' => 'no-cache']);
    }

    public function logoutAction(HttpFoundation\Request $request)
    {
        $request->getSession()->clear();
        return new HttpFoundation\Response("You're out.");
    }

    public function verifyAction(HttpFoundation\Request $request)
    {
        try {

            if ($this->oauth instanceof OAuth1\Service\ServiceInterface) {
                $token = $this->storage->retrieveAccessToken();
                $token = $this->oauth->requestAccessToken(
                    $request->query->get('oauth_token'),
                    $request->query->get('oauth_verifier'),
                    $token->getRequestTokenSecret()
                );

                //$userId = (int) $token->getExtraParams()['user_id'];
            } elseif ($this->oauth instanceof OAuth2\Service\ServiceInterface) {
                $this->oauth->requestAccessToken($request->get('code'));
            }

            $successUrl = $this->successUrl ?: $request->getSession()->get('oauth.success_url');
            $request->getSession()->remove('oauth.success_url');

            if (!$successUrl) {
                throw new \RuntimeException('Did not find oauth.success_url.');
            }

            return new HttpFoundation\RedirectResponse($successUrl);
        } catch (TokenResponseException $e) {
            // TODO: figure out some sane way to log this
            error_log($e->getMessage());

            return new HttpFoundation\RedirectResponse($this->failureUrl);
        }
    }
}

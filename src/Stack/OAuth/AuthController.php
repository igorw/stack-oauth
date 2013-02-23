<?php

namespace Stack\OAuth;

use OAuth\OAuth1\Service\ServiceInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthController
{
    private $storage;
    private $oauth;
    private $successUrl;

    public function __construct(TokenStorageInterface $storage, ServiceInterface $oauth, $successUrl, $failureUrl)
    {
        $this->storage = $storage;
        $this->oauth = $oauth;
        $this->successUrl = $successUrl;
        $this->failureUrl = $failureUrl;
    }

    public function authAction(Request $request)
    {
        $token = $this->oauth->requestRequestToken();
        $url = $this->oauth->getAuthorizationUri(['oauth_token' => $token->getRequestToken()]);

        $request->getSession()->set('oauth.success_url', $request->headers->get('Referer'));

        return new RedirectResponse($url->getAbsoluteUri(), 302, ['Cache-Control' => 'no-cache']);
    }

    public function verifyAction(Request $request)
    {
        try {
            $token = $this->storage->retrieveAccessToken();
            $token = $this->oauth->requestAccessToken(
                $request->query->get('oauth_token'),
                $request->query->get('oauth_verifier'),
                $token->getRequestTokenSecret()
            );
            $userId = (int) $token->getExtraParams()['user_id'];

            error_log("Authed: $userId");

            $successUrl = $this->successUrl ?: $request->getSession()->get('oauth.success_url');
            $request->getSession()->remove('oauth.success_url');

            if (!$successUrl) {
                error_log('Did not find oauth.success_url.');

                return new RedirectResponse($this->failureUrl);
            }

            return new RedirectResponse($successUrl);
        } catch (TokenResponseException $e) {
            error_log($e->getMessage());

            return new RedirectResponse($this->failureUrl);
        }
    }
}

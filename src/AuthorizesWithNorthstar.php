<?php

namespace DoSomething\Gateway;

use DoSomething\Gateway\Contracts\OAuthBridgeContract;
use DoSomething\Gateway\Exceptions\InternalException;
use DoSomething\Gateway\Exceptions\UnauthorizedException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

trait AuthorizesWithNorthstar
{
    /**
     * The authorization server URL (for example, Northstar).
     *
     * @var string
     */
    protected $authorizationServerUri;

    /**
     * The grant to use for authorization: supported values are either
     * 'authorization_code' or 'client_credentials'.
     *
     * @var string
     */
    protected $grant;

    /**
     * A queued access token to use for the next request.
     *
     * @var AccessToken|null
     */
    protected $token;

    /**
     * The OAuth2 configuration array, keyed by grant name.
     *
     * @var string
     */
    protected $config;

    /**
     * The class name of the OAuth framework bridge. This allows us to
     * interact with the application framework in a standardized way.
     *
     * @var string
     */
    protected $bridge;

    /**
     * The league/oauth2-client authorization server.
     *
     * @var NorthstarOAuthProvider
     */
    private $authorizationServer;

    /**
     * Run custom tasks before making a request.
     *
     * @see RestApiClient@raw
     */
    protected function runAuthorizesWithNorthstarTasks($method, &$path, &$options, &$withAuthorization)
    {
        // By default, we append the authorization header to every request.
        if ($withAuthorization) {
            $authorizationHeader = $this->getAuthorizationHeader();
            if (empty($options['headers'])) {
                $options['headers'] = [];
            }

            $options['headers'] = array_merge($this->defaultHeaders, $options['headers'], $authorizationHeader);
        }
    }

    /**
     * Authorize a machine based on the given client credentials.
     *
     * @return mixed
     */
    protected function getTokenByClientCredentialsGrant()
    {
        $token = $this->getAuthorizationServer()->getAccessToken('client_credentials', [
            'scope' => $this->config['client_credentials']['scope'],
        ]);

        $this->getFrameworkBridge()->persistClientToken(
            $this->config['client_credentials']['client_id'],
            $token->getToken(),
            $token->getExpires(),
            $token->getValues()['role']
        );

        return $token;
    }

    /**
     * Authorize a user by redirecting to Northstar's single sign-on page.
     *
     * @param string $code
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    protected function getTokenByAuthorizationCodeGrant($code)
    {
        try {
            $token = $this->getAuthorizationServer()->getAccessToken('authorization_code', [
                'code' => $code,
            ]);

            $this->getFrameworkBridge()->persistUserToken($token);

            return $token;
        } catch (IdentityProviderException $e) {
            return null;
        }
    }

    /**
     * Handle the OpenID Connect authorization flow.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param string $url - The destination URL to redirect to on a successful login.
     * @param string $destination - the title for the post-login destination
     * @param array  $options - Array of options to apply
     * @return ResponseInterface
     * @throws InternalException
     */
    public function authorize(ServerRequestInterface $request, ResponseInterface $response, $url = '/', $destination = null, $options = []) // TODO: Merge $url & $destination into $options
    {
        // Make sure we're making request with the authorization_code grant.
        $this->asUser();

        $url = $this->getFrameworkBridge()->prepareUrl($url);
        $query = $request->getQueryParams();

        // If we don't have an authorization code then make one and redirect.
        if (! isset($query['code'])) {
            $params = array_merge($options, [
                'scope' => $this->config['authorization_code']['scope'],
                'destination' => ! empty($destination) ? $destination : null,
            ]);
            $authorizationUrl = $this->getAuthorizationServer()->getAuthorizationUrl($params);

            // Get the state generated for you and store it to the session.
            $state = $this->getAuthorizationServer()->getState();
            $this->getFrameworkBridge()->saveStateToken($state);

            // Redirect the user to the authorization URL.
            return $response->withStatus(302)->withHeader('Location', $authorizationUrl);
        }

        // Check given state against previously stored one to mitigate CSRF attack
        if (! (isset($query['state']) && $query['state'] === $this->getFrameworkBridge()->getStateToken())) {
            throw new InternalException('[authorization_code]', 500, 'The OAuth state field did not match.');
        }

        $token = $this->getTokenByAuthorizationCodeGrant($query['code']);
        if (! $token) {
            throw new InternalException('[authorization_code]', 500, 'The authorization server did not return a valid access token.');
        }

        // Find or create a local user account, and create a session for them.
        $user = $this->getFrameworkBridge()->getOrCreateUser($token->getResourceOwnerId());
        $this->getFrameworkBridge()->login($user, $token);

        return $response->withStatus(302)->withHeader('Location', $url);
    }

    /**
     * Log a user out of the application and SSO service.
     *
     * @param ResponseInterface $response
     * @param string $destination
     * @return ResponseInterface
     */
    public function logout(ResponseInterface $response, $destination = '/')
    {
        // Make sure we're making request with the authorization_code grant.
        $this->asUser();

        $this->getFrameworkBridge()->logout();

        $destination = $this->getFrameworkBridge()->prepareUrl($destination);
        $ssoLogoutUrl = config('services.northstar.url').'/logout?redirect='.$destination;

        return $response->withStatus(302)->withHeader('Location', $ssoLogoutUrl);
    }

    /**
     * Re-authorize a user based on their stored refresh token.
     *
     * @param AccessToken $oldToken
     * @return AccessToken
     */
    public function getTokenByRefreshTokenGrant(AccessToken $oldToken)
    {
        try {
            $token = $this->getAuthorizationServer()->getAccessToken('refresh_token', [
                'refresh_token' => $oldToken->getRefreshToken(),
                'scope' => $this->config[$this->grant]['scope'],
            ]);

            $this->getFrameworkBridge()->persistUserToken($token);

            return $token;
        } catch (IdentityProviderException $e) {
            $this->getFrameworkBridge()->requestUserCredentials();

            return null;
        }
    }

    /**
     * Invalidate the authenticated user's refresh token.
     */
    public function invalidateCurrentRefreshToken()
    {
        if ($this->grant === 'client_credentials') {
            return;
        }

        $token = $this->getAccessToken();
        if ($token) {
            $this->invalidateRefreshToken($token);
        }
    }

    /**
     * Invalidate the refresh token for the given access token.
     *
     * @param AccessToken $token
     */
    public function invalidateRefreshToken(AccessToken $token)
    {
        $this->getAuthorizationServer()->getAuthenticatedRequest('DELETE',
            $this->authorizationServerUri . '/v2/auth/token', $token, [
                'json' => [
                    'token' => $token->getRefreshToken(),
                ],
            ]);

        $user = $this->getFrameworkBridge()->getUser($token->getResourceOwnerId());
        $user->clearOAuthToken();
    }

    /**
     * Specify which grant is used for this request.
     *
     * @param $grant
     * @return $this
     */
    public function usingGrant($grant)
    {
        $this->grant = $grant;

        return $this;
    }

    /**
     * Specify that the next request should use the client credentials grant.
     *
     * @return $this
     */
    public function asClient()
    {
        return $this->usingGrant('client_credentials');
    }

    /**
     * Specify that the next request should use the authorization code grant.
     *
     * @return $this
     */
    public function asUser()
    {
        return $this->usingGrant('authorization_code');
    }

    /**
     * Make request using the provided access token (for example, to make
     * a request to another service in order to complete an API request).
     *
     * @param AccessToken $token
     * @return $this
     */
    public function withToken(AccessToken $token)
    {
        $this->grant = 'provided_token';
        $this->token = $token;

        return $this;
    }

    /**
     * Get the access token from the repository based on the chosen grant.
     *
     * @return AccessToken|null
     * @throws \Exception
     */
    protected function getAccessToken()
    {
        switch ($this->grant) {
            case 'provided_token':
                return $this->token;

            case 'client_credentials':
                return $this->getFrameworkBridge()->getClientToken();

            case 'authorization_code':
                $user = $this->getFrameworkBridge()->getCurrentUser();

                if (! $user) {
                    return null;
                }

                return $user->getOAuthToken();

            default:
                throw new \Exception('Unsupported grant type. Check $this->grant.');
        }
    }

    /**
     * Get a new access token based on the chosen grant.
     *
     * @param $token
     * @return mixed
     * @throws \Exception
     */
    protected function refreshAccessToken($token)
    {
        switch ($this->grant) {
            case 'provided_token':
                throw new UnauthorizedException('[internal]', 'The provided token expired.');

            case 'client_credentials':
                return $this->getTokenByClientCredentialsGrant();

            case 'authorization_code':
                return $this->getTokenByRefreshTokenGrant($token);

            default:
                throw new \Exception('Unsupported grant type. Check $this->grant.');
        }
    }

    /**
     * Get the authorization header for a request, if needed.
     * Overrides this empty method in RestApiClient.
     *
     * @param bool $forceRefresh - Should the token be refreshed, even if expiration timestamp hasn't passed?
     * @return null|string
     * @throws \Exception
     */
    protected function getAuthorizationHeader($forceRefresh = false)
    {
        $token = $this->getAccessToken();

        // If the token is expired, fetch a new one before making the request.
        if (! $token || ($token && $token->hasExpired()) || $forceRefresh) {
            $token = $this->refreshAccessToken($token);
        }

        return $this->getAuthorizationServer()->getHeaders($token);
    }

    /**
     * Handle unauthorized exceptions.
     *
     * @param $endpoint - The path that
     * @param $response
     * @param $method - The HTTP method for the request that triggered the error, for optionally resending.
     * @param $path - The path for the request that triggered the error, for optionally resending.
     * @param $options - The options for the request that triggered the error, for optionally resending.
     * @return \GuzzleHttp\Psr7\Response|void
     * @throws UnauthorizedException
     */
    public function handleUnauthorizedException($endpoint, $response, $method, $path, $options)
    {
        // If we got an "Access Denied" error from an invalid access token, attempt to force-refresh it once.
        if (! empty($response->error) && $response->error === 'access_denied' && $this->getAttempts() < 2) {
            $options['headers']['Authorization'] = $this->getAuthorizationHeader(true);

            return $this->send($method, $path, $options, false);
        }

        throw new UnauthorizedException($endpoint, json_encode($response));
    }

    /**
     * Get the authorization server.
     */
    protected function getAuthorizationServer()
    {
        if (! $this->authorizationServer) {
            $config = $this->config[$this->grant];

            $options = [
                'url' => $this->authorizationServerUri,
                'clientId' => $config['client_id'],
                'clientSecret' => $config['client_secret'],
            ];

            if (! empty($config['redirect_uri'])) {
                $options['redirectUri'] = $this->getFrameworkBridge()->prepareUrl($config['redirect_uri']);
            }

            // Allow setting a custom handler (for mocking requests in tests).
            if (! empty($this->config['handler'])) {
                $options['handler'] = $this->config['handler'];
            }

            $this->authorizationServer = new NorthstarOAuthProvider($options);
        }

        return $this->authorizationServer;
    }

    /**
     * Get the OAuth repository used for storing & retrieving tokens.
     * @return OAuthBridgeContract $repository
     * @throws \Exception
     */
    private function getFrameworkBridge()
    {
        if (! class_exists($this->bridge)) {
            throw new \Exception('You must provide an implementation of OAuthBridgeContract to store tokens.');
        }

        return new $this->bridge();
    }

    /**
     * Clean up after a request is sent.
     *
     * @return void
     */
    protected function cleanUp()
    {
        // Reset back to the default grant & empty any provided token.
        $this->grant = $this->config['grant'];
        $this->token = null;
    }
}

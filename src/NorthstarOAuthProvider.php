<?php

namespace DoSomething\Gateway;

use DoSomething\Gateway\Resources\NorthstarUser;
use Lcobucci\JWT\Parser;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class NorthstarOAuthProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * The authorization server URL (e.g. https://northstar.dosomething.org)
     *
     * @var string
     */
    protected $url;

    /**
     * Constructs an OAuth 2.0 service provider.
     *
     * @param array $options An array of options to set on this provider.
     * @param array $collaborators - Collaborators that may be used to override default behavior.
     */
    public function __construct($options, $collaborators = [])
    {
        $this->url = $options['url'];

        parent::__construct($options, $collaborators);
    }

    /**
     * Returns the base URL for authorizing a client.
     * E.g. https://oauth.service.com/authorize
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->url . '/authorize';
    }

    /**
     * Returns the base URL for requesting an access token.
     * E.g. https://oauth.service.com/token
     *
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->url . '/v2/auth/token';
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     * E.g. https://oauth.service.com/user
     *
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->url . '/v1/profile';
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return ['user'];
    }

    /**
     * Returns the string that should be used to separate scopes when building
     * the URL for requesting an access token.
     *
     * @return string
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }

    /**
     * Returns a prepared request for requesting an access token.
     *
     * @param array $params - Query string parameters
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function getAccessTokenRequest(array $params)
    {
        if (isset($params['scope']) && is_array($params['scope'])) {
            $separator = $this->getScopeSeparator();
            $params['scope'] = implode($separator, $params['scope']);
        }

        return parent::getAccessTokenRequest($params);
    }

    /**
     * Prepares an parsed access token response for a grant.
     *
     * @param  mixed $result
     * @return array
     */
    protected function prepareAccessTokenResponse(array $result)
    {
        $jwt = (new Parser())->parse($result['access_token']);
        $result['resource_owner_id'] = $jwt->getClaim('sub');
        $result['role'] = $jwt->getClaim('role');

        return $result;
    }

    /**
     * Checks a provider response for errors.
     *
     * @param  ResponseInterface $response
     * @param  array|string $data Parsed response data
     * @throws IdentityProviderException
     * @throws \Exception
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        // Standard error response format
        if (! empty($data['error'])) {
            throw new IdentityProviderException(
                $response->getReasonPhrase(),
                $response->getStatusCode(),
                (string) $response->getBody()
            );
        }
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request (i.e. the authorize user's profile).
     *
     * @param  array $response
     * @param  AccessToken $token
     * @return ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new NorthstarUser($response['data']);
    }

    /**
     * Return the list of options that can be passed to the HttpClient
     *
     * @param array $options An array of options to set on this provider.
     *     Options include `clientId`, `clientSecret`, `redirectUri`, and `state`.
     *     Individual providers may introduce more options, as needed.
     * @return array The options to pass to the HttpClient constructor
     */
    protected function getAllowedClientOptions(array $options)
    {
        $options = parent::getAllowedClientOptions($options);

        return array_merge($options, ['handler']);
    }
}

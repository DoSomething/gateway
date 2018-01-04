<?php

namespace DoSomething\Gateway\Testing;

use Carbon\Carbon;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

/**
 * @mixin \Illuminate\Foundation\Testing\TestCase
 */
trait WithOAuthTokens
{
    protected function randomUserId()
    {
        return array_random([
            '5554eac1a59dbf117e8b4567',
            '5570b6cea59dbf3b7a8b4567',
            '5575e568a59dbf3b7a8b4572',
            '55844355a59dbfa93d8b458d',
            '5589c991a59dbfa93d8b45ae',
            '559442cca59dbfca578b4bf3',
        ]);
    }

    /**
     * Create an administrator & log them in to the application.
     *
     * @return $this
     */
    public function withStandardAccessToken()
    {
        return $this->withAccessToken($this->randomUserId(), 'user');
    }

    /**
     * Create an administrator & log them in to the application.
     *
     * @return $this
     */
    public function withAdminAccessToken()
    {
        return $this->withAccessToken($this->randomUserId(), 'admin');
    }

    /**
     * Create a signed JWT to authorize resource requests.
     *
     * @param string $userId
     * @param array $scopes
     * @return $this
     */
    public function withAccessToken($userId, $role = 'user', $scopes = ['user', 'role:staff', 'role:admin'])
    {
        $privateKey = dirname(__FILE__) . '/example-private.key';
        $jti = hash('sha256', mt_rand());
        $now = Carbon::now();

        $token = (new Builder())
            ->setIssuer(url(config('services.northstar.url')))
            ->setAudience('phpunit')
            ->setId($jti, true)
            ->setIssuedAt($now->timestamp)
            ->setNotBefore($now->timestamp)
            ->setExpiration($now->addHour()->timestamp)
            ->setSubject($userId)
            ->set('role', $role)
            ->set('scopes', $scopes)
            ->sign(new Sha256(), new Key('file://' . $privateKey))
            ->getToken();

        // Attach the token to the request.
        $header = $this->transformHeadersToServerVars(['Authorization' => 'Bearer ' . (string) $token]);
        $this->serverVariables = array_merge($this->serverVariables, $header);

        // Use the bundled public key for verifying test tokens.
        config(['auth.providers.northstar.key' => dirname(__FILE__) . '/example-public.key']);
        config(['services.northstar.key' => dirname(__FILE__) . '/example-public.key']);

        return $this;
    }
}

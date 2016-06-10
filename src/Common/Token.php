<?php

namespace DoSomething\Northstar\Common;

class Token
{
    /**
     * The user ID associated with this token.
     *
     * @var string
     */
    protected $userId;

    /**
     * The encoded access token.
     *
     * @var string
     */
    protected $accessToken;

    /**
     * The encoded refresh token.
     *
     * @var string
     */
    protected $refreshToken;

    /**
     * The expiration timestamp.
     *
     * @var int
     */
    protected $expiration;

    /**
     * Make a new token entity.
     */
    public function __construct($userId, $accessToken, $refreshToken, $expiration)
    {
        $this->userId = $userId;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiration = (int) $expiration;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @return int
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * Will this token expire within the next minute?
     *
     * @return bool
     */
    public function willExpireSoon()
    {
        return $this->getExpiration() < time() + 60;
    }
}

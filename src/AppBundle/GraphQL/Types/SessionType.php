<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;

/**
 * Representation of the GraphQL "Session" type.
 */
class SessionType extends BaseType
{
    /** @var \LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType The current user as UserType */
    private $user = null;
    /** @var string The apiKey */
    private $apiKey = null;
    /** @var string The expiration date of the session */
    private $expiresAt = null;

    /**
     * Returns the current user.
     * @return \LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the user field to a given value.
     * @param \LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType $user
     */
    public function setUser(UserType $user)
    {
        $this->user = $user;
    }

    /**
     * Returns the api key
     * @return string|null
     */
    public function getAuthToken()
    {
        return $this->apiKey;
    }

    /**
     * Sets the api key to a given value.
     * @param string $apiKey
     */
    public function setAuthToken(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Returns the expiration date of the session.
     * @return string|null
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Sets the expires-at to a given value.
     * @param string $expiresAt
     */
    public function setExpiresAt(string $expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }
}

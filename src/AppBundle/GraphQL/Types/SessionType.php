<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;

/**
 * A session.
 */
class SessionType implements TypeInterface
{
    /** @var Game The game instance. */
    private $game;
    
    /** @var closure Returns the a user type.. */
    public $user = null;
    /** @var closure Returns the apiKey */
    public $apiKey = null;
    /** @var closure Returns the expiration date of the session */
    public $expiresAt = null;
    
    public function __construct(Game $game)
    {
        $this->game = $game;
    }
    
    /**
     * Sets the user field to a given value.
     * @param \LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType $user
     */
    public function setUser(UserType $user)
    {
        $this->user = function() use ($user) { return $user; };
    }
    
    /**
     * Sets the api key to a given value.
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;
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

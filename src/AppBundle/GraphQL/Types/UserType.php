<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;
use LotGD\Crate\GraphQL\Models\User;

/**
 * GraphQL User type.
 */
class UserType implements TypeInterface
{
    /** @var Game The game instance. */
    private $game;
    /** @var User the user instance */
    private $user;
    
    /** @var closure Returns the a user type.. */
    public $id = null;
    /** @var closure Returns the user name */
    public $name = null;
    
    public function __construct(Game $game, User $user = null)
    {
        $this->game = $game;
        $this->id = function() use ($user) { return (string)$user->getId(); };
        $this->name = function() use ($user) { return $user->getName(); };
    }
}

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
    private $_game;
    /** @var User the user instance */
    private $_user;
    
    /** @var closure Returns the a user type.. */
    public $id = null;
    /** @var closure Returns the user name */
    public $name = null;
    /** @var closue Yields a list of characters */
    public $characters = null;
    
    public function __construct(Game $game, User $user = null)
    {
        $this->_game = $game;
        $this->_user = $user;
        
        $this->id = function() use ($user) { return (string)$user->getId(); };
        $this->name = function() use ($user) { return $user->getName(); };
        $this->characters = function() { return $this->listCharacters(); };
    }
    
    /**
     * Returns a UserType for an user with a given id.
     * @param Game $game
     * @param int $userId
     * @return type
     */
    public static function fromId(Game $game, int $userId)
    {
        $em = $game->getEntityManager();
        $user = $em->getRepository(User::class)->find($userId);
        
        return ($user ? new static($game, $user) : null);
    }
    
    /**
     * Returns a UserType with for an user with a given name.
     * @param Game $game
     * @param string $userName
     * @return type
     */
    public static function fromName(Game $game, string $userName)
    {
        $em = $game->getEntityManager();
        $user = $em->getRepository(User::class)->findOneBy(["name" => $userName]);
        
        return ($user ? new static($game, $user) : null);
    }
    
    /**
     * Returns a generator yielding a list of characters of this user.
     * @return \Generator
     * @yields CharacterType
     */
    protected function listCharacters(): \Generator
    {
        foreach ($this->_user->fetchCharacters() as $character) {
            yield new CharacterType($this->_game, $character);
        }
    }
}

<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;
use LotGD\Core\Models\Character;

/**
 * GraphQL ActionGroup type.
 */
class CharacterType implements TypeInterface
{
    /** @var Game The game instance. */
    private $_game;
    /** @var Viewpoint The viewpoint */
    private $_character;
    
    public function __construct(Game $game, Character $character = null)
    {
        $this->_game = $game;
        $this->_character = $character;
        
        $this->id = function() { return (string)$this->_character->getId(); };
        $this->name = function() { return (string)$this->_character->getName(); };
        $this->displayName = function() { return (string)$this->_character->getDisplayName(); };
    }
    
    public static function fromId(Game $game, int $characterId)
    {
        $em = $game->getEntityManager();
        $user = $em->getRepository(Character::class)->find($characterId);
        
        return ($user ? new CharacterType($game, $user) : null);
    }
    
    public static function fromName(Game $game, string $characterName)
    {
        $em = $game->getEntityManager();
        $user = $em->getRepository(Character::class)->findOneBy(["name" => $characterName]);
        
        return ($user ? new CharacterType($game, $user) : null);
    }
}

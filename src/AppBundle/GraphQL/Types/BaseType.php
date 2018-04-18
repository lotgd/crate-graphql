<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;

/**
 * The base type of graphql types which should get returned in resolvers.
 *
 * Classes extending this BaseType need to provide a getter for every property
 * that corresponding to the type.
 */
abstract class BaseType implements TypeInterface
{
    private $gameInstance;
    private $guarded = false;

    /**
     * {@inheritDoc}
     */
    public function __construct(Game $game)
    {
        $this->gameInstance = $game;
    }

    /**
     * Returns the game object.
     * @return Game
     */
    protected function getGameObject(): Game
    {
        return $this->gameInstance;
    }

    /**
     * Flags a type as guarded.
     */
    public function flagAsGuarded(): void
    {
        $this->guarded = true;
    }

    /**
     * Returns whether the type is guarded or not.
     *
     * If a user does not own a character, they should not have access to certain properties, such as current funds,
     * or even non-interesting stats like the number of turns. Such information should only be returned by getters
     * if this method returns true. An example is in CharacterType which only adds private stats to the stat field
     * if the type is guarded.
     * @return bool
     */
    public function isGuarded(): bool
    {
        return $this->guarded;
    }
}

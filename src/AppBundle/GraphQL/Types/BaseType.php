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

    public function _setGuarded(): void
    {
        $this->guarded = true;
    }

    public function isGuarded(): bool
    {
        return $this->guarded;
    }
}

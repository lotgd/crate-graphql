<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;
use LotGD\Core\Models\Character;

/**
 * GraphQL Character type.
 */
class CharacterType extends BaseType
{
    /** @var Viewpoint The viewpoint */
    private $characterEntity;

    /**
     * @param Game $game
     * @param Character $character
     */
    public function __construct(Game $game, Character $character = null)
    {
        parent::__construct($game);
        $this->characterEntity = $character;
    }

    /**
     * Returns the character id
     * @return string
     */
    public function getId(): string
    {
        return (string)$this->characterEntity->getId();
    }

    /**
     * Returns the name.
     * @return string
     */
    public function getName(): string
    {
        return $this->characterEntity->getName();
    }

    /**
     * Returns the display name.
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->characterEntity->getDisplayName();
    }
}

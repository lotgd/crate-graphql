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
     * Returns a CharacterType by a given id.
     * @param Game $game
     * @param int $characterId
     * @return self|null Returns a CharacterType matching the parameter or null if not found.
     */
    public static function fromId(Game $game, int $characterId)
    {
        $em = $game->getEntityManager();
        $character = $em->getRepository(Character::class)->find($characterId);

        return ($character ? new static($game, $character) : null);
    }

    /**
     * Returns a CharacterType by a given name.
     * @param Game $game
     * @param string $characterName
     * @return self|null Returns a CharacterType matching the parameter or null if not found.
     */
    public static function fromName(Game $game, string $characterName)
    {
        $em = $game->getEntityManager();
        $character = $em->getRepository(Character::class)->findOneBy(["name" => $characterName]);

        return ($character ? new static($game, $character) : null);
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

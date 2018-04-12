<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use Doctrine\Common\Util\Debug;
use LotGD\Core\Events\EventContextData;
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
     * Returns type
     * @return string
     */
    public function getType(): string
    {
        return "Character";
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

    /**
     * Get character level
     * @return int
     */
    public function getLevel(): int
    {
        return $this->characterEntity->getLevel();
    }

    /**
     * Get character attack
     * @return int
     */
    public function getAttack(): int
    {
        return $this->characterEntity->getAttack();
    }

    /**
     * Get character defense
     * @return int
     */
    public function getDefense(): int
    {
        return $this->characterEntity->getDefense();
    }

    /**
     * Gets current health
     * @return int
     */
    public function getHealth(): int
    {
        return $this->characterEntity->getHealth();
    }

    /**
     * Gets maximum health
     * @return int
     */
    public function getMaxHealth(): int
    {
        return $this->characterEntity->getMaxHealth();
    }


    /**
     * Returns base stats
     * @return array
     */
    private function returnBaseStats(): array
    {
        $stats = [
        ];

        $eventData = $this->getGameObject()->getEventManager()->publish(
            "h/lotgd/crate-graphql/characterStats/public",
            EventContextData::create(["character" => $this->characterEntity, "value" => $stats])
        );

        $stats = $eventData->get("value");

        return $stats;
    }

    /**
     * Returns public stats
     * @return array
     */
    public function getPublicStats(): array
    {
        $stats = $this->returnBaseStats();

        return $stats;
    }

    /**
     * Returns public and private stats.
     * @return array
     */
    public function getPrivateStats(): array
    {
        $publicStats = $this->returnBaseStats();
        $privateStats = [];

        $eventData = $this->getGameObject()->getEventManager()->publish(
            "h/lotgd/crate-graphql/characterStats/private",
            EventContextData::create(["character" => $this->characterEntity, "value" => $privateStats])
        );

        $privateStats = $eventData->get("value");

        $returnValue = array_merge($publicStats, $privateStats);

        return $returnValue;
    }
}

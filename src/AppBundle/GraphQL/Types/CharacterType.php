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

    public function getType()
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

    private function returnBaseStats(): array
    {
        $stats = [
            new CharacterStatIntType("lotgd/core/level", "Level", $this->characterEntity->getLevel()),
            new CharacterStatIntType("lotgd/core/attack", "Attack", $this->characterEntity->getAttack()),
            new CharacterStatIntType("lotgd/core/defense", "Defense", $this->characterEntity->getDefense()),
            new CharacterStatRangeType("lotgd/core/health", "Health", $this->characterEntity->getHealth(), $this->characterEntity->getMaxHealth()),
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

        return array_combine($publicStats, $privateStats);
    }
}

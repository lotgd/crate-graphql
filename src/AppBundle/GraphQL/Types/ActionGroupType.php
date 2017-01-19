<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use Generator;

use LotGD\Core\Game;
use LotGD\Core\ActionGroup;

/**
 * GraphQL ActionGroup type.
 */
class ActionGroupType extends BaseType
{
    /** @var ActionGroup The action-group entity */
    private $actionGroupEntity;

    /**
     * @param Game $game
     * @param ActionGroup $actionGroup
     */
    public function __construct(Game $game, ActionGroup $actionGroup = null)
    {
        parent::__construct($game);
        $this->actionGroupEntity = $actionGroup;
    }

    /**
     * Yields a list of ActionTypes.
     * @yield ActionType
     */
    public function getActions(): Generator
    {
        $actions = $this->actionGroupEntity->getActions();

        foreach ($actions as $action) {
            yield new ActionType($this->getGameObject(), $action);
        }
    }

    /**
     * Returns the action-group id.
     * @return string
     */
    public function getId(): string
    {
        return $this->actionGroupEntity->getId();
    }

    /**
     * Returns the action-group title.
     * @return string
     */
    public function getTitle(): string
    {
        return $this->actionGroupEntity->getTitle();
    }

    /**
     * Returns the action-group sort key.
     * @return int
     */
    public function getSortKey(): int
    {
        return $this->actionGroupEntity->getSortKey();
    }
}

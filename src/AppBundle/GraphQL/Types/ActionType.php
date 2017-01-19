<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;
use LotGD\Core\Action;
use LotGD\Core\Models\Scene;

/**
 * GraphQL Action type.
 */
class ActionType extends BaseType
{
    /** @var Action The action entity */
    private $actionEntity;

    public function __construct(Game $game, Action $action = null)
    {
        parent::__construct($game);
        $this->actionEntity = $action;
    }

    /**
     * Returns the title of the action
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getGameObject()
            ->getEntityManager()
            ->getRepository(Scene::class)
            ->find(
                $this->actionEntity->getDestinationSceneId()
            )
            ->getTitle();
    }

    /**
     * Returns the id of the action
     * @return string
     */
    public function getId(): string
    {
        return $this->actionEntity->getId();
    }
}

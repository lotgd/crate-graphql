<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;
use LotGD\Core\Models\Viewpoint;

/**
 * Representation of the GraphQL "Viewpoint" type.
 */
class ViewpointType extends BaseType
{
    /** @var Viewpoint The viewpoint */
    private $viewpointEntity;

    /**
     * @param Game $game
     * @param Viewpoint $viewpointEntity
     */
    public function __construct(Game $game, Viewpoint $viewpointEntity =  null)
    {
        parent::__construct($game);
        $this->viewpointEntity = $viewpointEntity;
    }

    /**
     * Returns the viewpoint title.
     * @return string
     */
    public function getTitle(): string
    {
        return $this->viewpointEntity->getTitle();
    }

    /**
     * Returns the viewpoint description.
     * @return string
     */
    public function getDescription(): string
    {
        return $this->viewpointEntity->getDescription();
    }

    /**
     * Returns the viewpoint template.
     * @return string
     */
    public function getTemplate()
    {
        return $this->viewpointEntity->getTemplate();
    }

    /**
     * Yields a list of ActionGroupTypes
     * @yield ActionGroupType
     */
    public function getActionGroups()
    {
        $actionGroups = $this->viewpointEntity->getActionGroups();

        foreach ($actionGroups as $actionGroup) {
            yield new ActionGroupType($this->getGameObject(), $actionGroup);
        }
    }
}

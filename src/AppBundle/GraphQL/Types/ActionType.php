<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;
use LotGD\Core\Action;
use LotGD\Core\Models\Scene;

/**
 * GraphQL ActionGroup type.
 */
class ActionType
{
    /** @var Game The game instance. */
    private $_game;
    /** @var Viewpoint The viewpoint */
    private $_action;
    
    /** @var closure Returns the Action id. */
    public $id;
    /** @var closure Returns the Action title. */
    public $title;
    
    public function __construct(Game $game, Action $action = null)
    {
        $this->_game = $game;
        $this->_action = $action;
        
        $this->id = function() use ($action) { return $action->getId(); };
        $this->title = function() { return $this->getTitle(); };
    }
    
    /**
     * Returns title
     * @return string
     */
    public function getTitle(): string
    {
        return $this->_game->getEntityManager()->getRepository(Scene::class)
            ->find($this->_action->getDestinationSceneId())->getTitle();
    }
}

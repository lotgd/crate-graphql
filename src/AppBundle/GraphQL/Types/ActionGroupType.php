<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;
use LotGD\Core\ActionGroup;

/**
 * GraphQL ActionGroup type.
 */
class ActionGroupType
{
    /** @var Game The game instance. */
    private $_game;
    /** @var Viewpoint The viewpoint */
    private $_actionGroup;
    
    /** @var closure Returns the ActionGroup id. */
    public $id;
    /** @var closure Returns the ActionGroup title. */
    public $title;
    /** @var closure Returns the ActionGroup sortKey. */
    public $sortKey;
    /** @var closure Yields a list of ActionType. */
    public $actions;
    
    public function __construct(Game $game, ActionGroup $actionGroup = null)
    {
        $this->_game = $game;
        $this->_actionGroup = $actionGroup;
        
        $this->id = function() use ($actionGroup) { return $actionGroup->getId(); };
        $this->title = function() use ($actionGroup) { return $actionGroup->getTitle(); };
        $this->sortKey = function() use ($actionGroup) { return $actionGroup->getSortKey(); };
        $this->actions = function() { return $this->getActions(); };
    }
    
    /**
     * Yields a list of ActionTypes.
     * @yield ActionType
     */
    public function getActions()
    {
        $actions = $this->_actionGroup->getActions();
        
        foreach ($actions as $action) {
            yield new ActionType($this->_game, $action);
        }
    }
}

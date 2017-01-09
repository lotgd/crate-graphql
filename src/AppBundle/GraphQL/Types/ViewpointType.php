<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;
use LotGD\Core\Models\Viewpoint;

/**
 * GraphQL Viewpoint type.
 */
class ViewpointType implements TypeInterface
{
    /** @var Game The game instance. */
    private $_game;
    /** @var Viewpoint The viewpoint */
    private $_viewpoint;
    
    /** @var closure Returns the viewpoint title. */
    public $title;
    /** @var closure Returns the viewpoint description. */
    public $description;
    /** @var closure Returns the viewpoint template. */
    public $template;
    /** @var closure Yield a list of ActionGroupTypes. */
    public $actionGroups;
    
    public function __construct(Game $game, Viewpoint $viewpoint = null)
    {
        $this->_game = $game;
        $this->_viewpoint = $viewpoint;
        
        $this->title = function() use ($viewpoint) { return $viewpoint->getTitle(); };
        $this->description = function() use ($viewpoint) { return $viewpoint->getDescription(); };
        $this->template = function() use ($viewpoint) { return $viewpoint->getTemplate(); };
        $this->actionGroups = function() { return $viewpoint->getActionGroups(); };
    }
    
    /**
     * Yields a list of ActionGroupTypes
     * @yield ActionGroupType
     */
    public function getActionGroups()
    {
        $actionGroups = $this->_viewpoint->getActionGroups();
        
        foreach ($actionGroups as $actionGroup) {
            yield new ActionGroupType($this->_game, $actionGroup);
        }
    }
}

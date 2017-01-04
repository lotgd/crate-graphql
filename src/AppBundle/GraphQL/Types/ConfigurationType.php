<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;

/**
 * 
 */
class ConfigurationType
{
    public function __construct(Game $game)
    {
        $this->game = $game;
        $this->core = function() { return $this->getCore(); };
        $this->crate = function() { return $this->getCrate(); };
        $this->modules = function() { return $this->getModules(); };
    }
    
    public function getCore()
    {
        return new LibraryType($this->game, "lotgd/core");
    }
    
    public function getCrate()
    {
        return new LibraryType($this->game);
    }
    
    public function getModules()
    {
        foreach ($this->game->getModuleManager()->getModules() as $module) {
            yield new LibraryType($this->game, $module->getLibrary());
        }
    }
}

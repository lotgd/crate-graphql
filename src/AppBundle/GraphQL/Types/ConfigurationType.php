<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;

/**
 * Represents the Realm's configuration in GraphQL
 */
class ConfigurationType
{
    /** @var Game The game instance. */
    private $game;
    
    /** @var closure Returns the core library information */
    public $core;
    /** @var closure Returns the create library information (this package!) */
    public $crate;
    /** @var generator Generates a list of installed moduels and returns their library type */
    public $modules;
    
    /**
     * @param Game $game The game instance
     */
    public function __construct(Game $game)
    {
        $this->game = $game;
        $this->core = function() { return $this->getCore(); };
        $this->crate = function() { return $this->getCrate(); };
        $this->modules = function() { return $this->getModules(); };
    }
    
    /**
     * Returns the core library.
     * @return LibraryType
     */
    public function getCore(): LibraryType
    {
        return new LibraryType($this->game, "lotgd/core");
    }
    
    /**
     * Returns the crate library.
     * @return LibraryType
     */
    public function getCrate(): LibraryType
    {
        return new LibraryType($this->game);
    }
    
    /**
     * Returns a generator that yields a list of libraries of installed modules.
     * @return \Generator
     */
    public function getModules(): \Generator
    {
        foreach ($this->game->getModuleManager()->getModules() as $module) {
            yield new LibraryType($this->game, $module->getLibrary());
        }
    }
}

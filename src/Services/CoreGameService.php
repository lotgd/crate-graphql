<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Services;

use Doctrine\ORM\EntityManagerInterface;

use LotGD\Core\ {
    Bootstrap,
    Game,
    ModuleManager
};

/**
 * Description of CoreGameService
 */
class CoreGameService
{
    public $game;
    
    public function __construct()
    {
        if (substr(getcwd(), -4) === "/web") { 
            $this->game = Bootstrap::createGame(getcwd() . DIRECTORY_SEPARATOR . "..");
        } else {
            $this->game = Bootstrap::createGame(getcwd());
        }
    }
    
    /**
     * Returns the game instance
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->game;
    }
    
    /**
     * Returns the current game version
     * @return string
     */
    public function getVersion(): string
    {
        return $this->game->getVersion();
    }
    
    /**
     * Returns the EntityManager used by the game
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->game->getEntityManager();
    }
    
    /**
     * Returns the Module Manager used by the game
     * @return ModuleManager
     */
    public function getModuleManager(): ModuleManager
    {
        return $this->game->getModuleManager();
    }
}

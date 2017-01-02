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
 * A service to manage the lotgd/core procedure.
 *
 * This service initiates the lotgd/core game object and provides
 * proxy methods as abbreviations to ease the typing effort.
 */
class CoreGameService
{
    public $game;
    protected static $_game;

    /**
     * Creates the lotgd/core game object and decides which cwd to use.
     *
     * If the cwd is /web instead of the crate's root directory, this method
     * changes the cwd which the core uses. (This is important to make a distinction
     * between test runs (cwd = /), console runs (cwd = /) and web runs (/web, via /web/app.php).
     */
    public function __construct()
    {
        // Need to keep a static instance so that the game is the same over all instances of symfony.
        if (self::$_game) {
            $this->game = self::$_game;
        } else {
            if (substr(getcwd(), -4) === DIRECTORY_SEPARATOR . "web") {
                $this->game = Bootstrap::createGame(getcwd() . DIRECTORY_SEPARATOR . "..");
            } else {
                $this->game = Bootstrap::createGame(getcwd());
            }

            self::$_game = $this->game;
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

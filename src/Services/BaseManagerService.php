<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Services;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

use LotGD\Core\ {
    Game,
    ModuleManager
};

/**
 * Basic Manager Service to use with all services that need to access LotGD\Core\Game.
 */
abstract class BaseManagerService
{
    /** @var LotGD\Core\Game */
    protected $game = null;

    /**
     * Supply an optional game object via constructor
     * @param Game $game
     */
    public function __construct(Game $game = null)
    {
        $this->game = $game;
    }

    /**
     * Supply an optional game object via dependency injection from the CoreGameService
     * @param \LotGD\Crate\GraphQL\Services\CoreGameService $gameService
     */
    public function setCoreGameService(CoreGameService $gameService)
    {
        $this->game = $gameService->getGame();
    }

    /**
     * Returns the core's central Game class
     * @return Game
     */
    protected function getGame(): Game
    {
        return $this->game;
    }

    /**
     * Returns the entity manager.
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->game->getEntityManager();
    }

    /**
     * Returns the module manager.
     * @return ModuleManager
     */
    protected function getModuleManager(): ModuleManager
    {
        return $this->game->getModuleManager();
    }

    /**
     * Helper function to get exactly one match from a repository.
     * @param string $repositoryClass
     * @param type $arguments
     * @return type
     */
    protected function getOneBy(string $repositoryClass, $arguments)
    {
        return $this->getEntityManager()
            ->getRepository($repositoryClass)
            ->findOneBy($arguments);
    }

    /**
     * Helper function to get exactly one row from a repository by id.
     * @param string $repositoryClass
     * @param int $id
     * @return type
     */
    protected function getOneById(string $repositoryClass, int $id)
    {
        return $this->getEntityManager()
            ->getRepository($repositoryClass)
            ->find($id);
    }
}

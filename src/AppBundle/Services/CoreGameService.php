<?php
declare(strict_types=1);

namespace LotGD\Crate\WWW\AppBundle\Services;

use Doctrine\ORM\EntityManagerInterface;

use LotGD\Core\Bootstrap;
use LotGD\Core\ModuleManager;

/**
 * Description of CoreGameService
 */
class CoreGameService
{
    public $game;
    
    public function __construct()
    {
        // For now
        $handle = fopen(__DIR__ . "/../../../.env", "r");
        while (($line = fgets($handle))) {
            putenv(trim($line));
        }
        
        $this->game = Bootstrap::createGame();
    }
    
    public function getVersion()
    {
        return $this->game->getVersion();
    }
    
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->game->getEntityManager();
    }
    
    public function getModuleManager(): ModuleManager
    {
        return $this->game->getModuleManager();
    }
}

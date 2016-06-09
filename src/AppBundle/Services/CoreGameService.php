<?php
declare(strict_types=1);

namespace LotGD\Crate\WWW\AppBundle\Services;

use LotGD\Core\Bootstrap;

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
}

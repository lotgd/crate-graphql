<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;

/**
 * 
 */
class RealmType
{
    private $game;
    
    public $name;
    public $configuration;
    
    public function __construct(Game $game)
    {
        $this->game = $game;
        $this->name = "Test-Environment";
        $this->configuration = function() { return $this->getConfiguration(); };
    }
    
    public function getConfiguration()
    {
        return new ConfigurationType($this->game);
    }
}

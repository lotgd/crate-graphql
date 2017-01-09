<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;

/**
 * Represents the Realm type in graphql.
 */
class RealmType implements TypeInterface
{
    /** @var Game The game instance. */
    private $game;
    
    /** @var string The Realm's name. */
    public $name;
    /** @var closure Returns the Realm's configuration if accessed. */
    public $configuration;
    
    /**
     * @param Game $game The game instance
     */
    public function __construct(Game $game)
    {
        $this->game = $game;
        $this->name = "Test-Environment";
        $this->configuration = function() { return $this->getConfiguration(); };
    }
    
    /**
     * Returns the Realm's configuration as a ConfigurationType
     * @return \LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\ConfigurationType
     */
    public function getConfiguration(): ConfigurationType
    {
        return new ConfigurationType($this->game);
    }
}

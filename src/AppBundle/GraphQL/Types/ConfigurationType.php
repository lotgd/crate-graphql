<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use Generator;

use LotGD\Core\Game;

/**
 * Represents the Realm's configuration in GraphQL
 */
class ConfigurationType extends BaseType
{
    /**
     * @param Game $game The game instance
     */
    public function __construct(Game $game)
    {
        parent::__construct($game);
    }

    /**
     * Returns the core library.
     * @return LibraryType
     */
    public function getCore(): LibraryType
    {
        return new LibraryType($this->getGameObject(), "lotgd/core");
    }

    /**
     * Returns the crate library.
     * @return LibraryType
     */
    public function getCrate(): LibraryType
    {
        return new LibraryType($this->getGameObject());
    }

    /**
     * Returns a generator that yields a list of libraries of installed modules.
     * @return \Generator
     */
    public function getModules(): Generator
    {
        foreach ($this->getGameObject()->getModuleManager()->getModules() as $module) {
            yield new LibraryType($this->getGameObject(), $module->getLibrary());
        }
    }
}

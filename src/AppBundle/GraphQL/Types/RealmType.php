<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;

/**
 * Represents the Realm type in GraphQL.
 */
class RealmType extends BaseType
{
    /**
     * Returns the name of this realm.
     * @return string
     */
    public function getName(): string
    {
        return "Test-Environment";
    }

    /**
     * Returns the Realm's configuration as a ConfigurationType
     * @return \LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\ConfigurationType
     */
    public function getConfiguration(): ConfigurationType
    {
        return new ConfigurationType($this->getGameObject());
    }
}

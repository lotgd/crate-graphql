<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;

/**
 * The common interface of graphql types.
 */
interface TypeInterface
{
    public function __construct(Game $game);
}

<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Connections;

use Generator;

use Doctrine\Common\Collections\Collection;
use Overblog\GraphQLBundle\Definition\Argument;

use LotGD\Core\Game;
use LotGD\Core\Models\Character;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\CharacterType;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType;
use LotGD\Crate\GraphQL\Models\User;

/**
 * GraphQL Character Connection
 */
class CharacterConnection extends BaseConnection
{
    /**
     * @param UserType $user
     * @param Argument $args
     */
    public function __construct(UserType $user, Argument $args)
    {
        $this->setConnectionParameters(
            $user->getCharacters(),
            $args,
            $user
        );
    }

    /**
     * Creates a CharacterEdge type node for a given character/user.
     * @param User $user
     * @param CharacterType $character
     * @return type
     */
    public static function createEdgeFor(User $user, CharacterType $character)
    {
        $offset = count($user->getCharacters());

        return [
            "cursor" => static::encodeCursor($offset),
            "node" => $character,
        ];
    }
}
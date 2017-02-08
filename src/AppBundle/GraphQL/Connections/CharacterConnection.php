<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Connections;

use Doctrine\Common\Collections\Collection;
use Overblog\GraphQLBundle\Definition\Argument;

use LotGD\Core\Game;
use LotGD\Core\Models\Character;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\CharacterType;
use LotGD\Crate\GraphQL\Models\User;

/**
 * GraphQL ActionGroup type.
 */
class CharacterConnection
{
    private $user;
    private $args;
    private $offset;
    private $limit;
    private $length;

    private $hasNextPage = false;
    private $hasPreviousPage = false;

    public function __construct($user, $args)
    {
        $this->setConnectionParameters(
            $user->getCharacters(),
            $args
        );

        $this->user = $user;
    }

    protected function setConnectionParameters(Collection $collection, Argument $args)
    {
        $collectionLength = count($collection);

        // Calculate offset etc
        if (isset($args["first"])) {
            if (isset($args["after"])) {
                $offset = static::decodeCursor($args["after"]) + 1;
                $limit = $args["first"] > $collectionLength - $offset ? $collectionLength - $offset : $args["first"];
            } else {
                $offset = 0;
                $limit = $args["first"] > $collectionLength ? $collectionLength : $args["first"];
            }
        } elseif (isset($args["last"])) {
            if (isset($args["before"])) {

            } else {

            }
        } else {
            $limit = $collectionLength;
            $offset = 0;
        }

        $this->limit = $limit;
        $this->offset = $offset;
        $this->length = $collectionLength;
    }

    public static function decodeCursor(string $encoded): int
    {
        $decoded = base64_decode($encoded);
        $offset = intval(substr($decoded, 17));
        return $offset;
    }

    public static function encodeCursor(int $offset): string
    {
        $decoded = "collectionOffset:$offset";
        return base64_encode($decoded);
    }

    public function getEdges() {
        for ($i = $this->offset; $i < ($this->offset + $this->limit); $i++) {
            yield [
                "cursor" => static::encodeCursor($i),
                "__user" => $this->user,
            ];
        }
    }

    public function getPageInfo()
    {
        return [
            "hasNextPage" => ($this->length > $this->offset + $this->limit == true),
            "hasPreviousPage" => ($this->offset > 0 == true),
            "startCursor" => static::encodeCursor($this->offset),
            "endCursor" => static::encodeCursor($this->offset + $this->limit - 1),
        ];
    }

    public static function createEdgeFor(User $user, CharacterType $character)
    {
        $offset = count($user->getCharacters());

        return [
            "cursor" => static::encodeCursor($offset),
            "node" => $character,
        ];
    }
}
<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Error\UserError;

use LotGD\Crate\GraphQL\AppBundle\GraphQL\Connections\CharacterConnection;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\CharacterType;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType;
use LotGD\Crate\GraphQL\Exceptions\InputException;
use LotGD\Crate\GraphQL\Services\BaseManagerService;


class CharacterResolver extends BaseManagerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function resolve(Argument $args = null)
    {
        $characterEntity = null;

        if (isset($args["characterId"])) {
            $characterEntity = $this->container
                ->get("lotgd.crate.graphql.character_manager")
                ->findById((int)$args["characterId"]);
        } elseif (isset($args["characterName"])) {
            $characterEntity = $this->container
                ->get("lotgd.crate.graphql.character_manager")
                ->findByName($args["characterName"]);
        }

        if ($characterEntity !== null) {
            return new CharacterType($this->getGame(), $characterEntity);
        } else {
            return null;
        }
    }
}
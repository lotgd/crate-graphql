<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;

use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\CharacterType;
use LotGD\Crate\GraphQL\Services\BaseManagerService;


class CharacterResolver extends BaseManagerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function resolve(Argument $args = null)
    {
        $characterType = null;
        
        if (isset($args["characterId"])) {
            $characterType = CharacterType::fromId($this->game, (int)$args["characterId"]);
        } elseif (isset($args["characterName"])) {
            $characterType = CharacterType::fromName($this->game, $args["characterName"]);
        }
        
        return $characterType;
    }
}
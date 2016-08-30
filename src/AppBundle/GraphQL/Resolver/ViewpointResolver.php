<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;

use LotGD\Crate\GraphQL\Services\BaseManagerService;

class ViewpointResolver extends BaseManagerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function resolve(Argument $args = null)
    {
        if (empty($args["characterId"])) {
            return null;
        }

        $characterId = (int)$args["characterId"];
        $character = $this->container->get("lotgd.crate.graphql.character_manager")->findById($characterId);

        if (is_null($character)) {
            return null;
        } else {
            return [
                "title" => "Hallo",
                "description" => "Hallo.",
            ];
        }
    }
}

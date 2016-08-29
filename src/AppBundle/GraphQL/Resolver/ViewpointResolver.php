<?php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;

use LotGD\Crate\GraphQL\Services\BaseManagerService;
use LotGD\Crate\GraphQL\Models\User;

class ViewpointResolver extends BaseManagerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function resolve(Argument $args = null)
    {
        if (empty($args["characterId"])) {
            return null;
        }

        $characterId = $args["characterId"];
        $user = $this->container->get("lotgd.crate.graphql.user_manager")->findByName($username);
    }
}

<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;


use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType;
use LotGD\Crate\GraphQL\Models\User;
use LotGD\Crate\GraphQL\Services\BaseManagerService;

class UserResolver extends BaseManagerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function resolve(Argument $args = null)
    {
        if (empty($args["name"])) {
            return null;
        }

        $user = $this->container->get("lotgd.crate.graphql.user_manager")->findByName($args["name"]);

        if ($user instanceof User) {
            return new UserType($this->getGame(), $user);
        }
        else {
            return null;
        }
    }
}

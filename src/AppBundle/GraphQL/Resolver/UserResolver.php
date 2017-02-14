<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;


use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType;
use LotGD\Crate\GraphQL\Services\BaseManagerService;

class UserResolver extends BaseManagerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Resolves query for User with a given id user name, or returns null.
     * @param Argument $args
     * @return UserType
     */
    public function resolve(Argument $args = null)
    {
        $userEntity = null;

        if (isset($args["id"])) {
            $userEntity = $this->container
                ->get("lotgd.crate.graphql.user_manager")
                ->findById((int)$args["id"]);
        } elseif (isset($args["name"])) {
            $userEntity = $this->container
                ->get("lotgd.crate.graphql.user_manager")
                ->findByName($args["name"]);
        }

        if ($userEntity !== null) {
            return new UserType(
                $this->getGame(),
                $userEntity
            );
        } else {
            return null;
        }
    }
}

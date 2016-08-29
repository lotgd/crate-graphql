<?php

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;

use LotGD\Crate\GraphQL\Services\BaseManagerService;
use LotGD\Crate\GraphQL\Models\User;

class UserResolver extends BaseManagerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function resolve(Argument $args = null)
    {
        if (empty($args["name"])) {
            return null;
        }
        
        $username = $args["name"];
        $user = $this->container->get("lotgd.crate.graphql.user_manager")->findByName($username);
        
        if ($user instanceof User) {
            return [
                "id" => (string)$user->getId(),
                "name" => $user->getName()
            ];
        }
        else {
            return null;
        }
    }
}

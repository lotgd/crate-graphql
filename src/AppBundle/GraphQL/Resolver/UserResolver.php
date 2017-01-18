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
        $userType = null;
        
        if (isset($args["id"])) {
            $userType = UserType::fromId($this->getGame(), (int)$args["id"]);
        } elseif (isset($args["name"])) {
            $userType = UserType::fromName($this->getGame(), $args["name"]);
        }
        
        return $userType;
    }
}

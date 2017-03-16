<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver;

use LotGD\Core\Models\Permission;
use LotGD\Core\PermissionManager;
use LotGD\Crate\GraphQL\Tools\ManagerAwareTrait;
use Overblog\GraphQLBundle\Error\UserError;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;


use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType;
use LotGD\Crate\GraphQL\Services\BaseManagerService;

class UserResolver extends BaseManagerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ManagerAwareTrait;

    /**
     * Resolves query for User with a given id user name, or returns null.
     * @param Argument $args
     * @return UserType
     */
    public function resolve(Argument $args = null)
    {
        $userEntity = null;

        if (isset($args["id"])) {
            $userEntity = $this->getUserManager()
                ->findById((int)$args["id"]);
        } elseif (isset($args["name"])) {
            $userEntity = $this->getUserManager()
                ->findByName($args["name"]);
        }

        // check access right. user node should only be accessible to current user or to administration.
        if ($this->getAuthorizationService()->isLoggedin() === false) {
            throw new UserError("Accessing this field is not allowed for anonymous users.");
        } elseif (
            $userEntity !== $this->getAuthorizationService()->getCurrentUser() and
            $this->getAuthorizationService()->isAllowed(PermissionManager::Superuser) === false
        ) {
            throw new UserError("Accessing this field with this parameters is not allowed.");
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

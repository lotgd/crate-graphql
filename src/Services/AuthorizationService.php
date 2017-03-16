<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Services;

use LotGD\Core\PermissionManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\TypeGuardian;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType;
use LotGD\Crate\GraphQL\Models\User;

class AuthorizationService extends BaseManagerService
{
    use ContainerAwareTrait;

    private $permissionManager;
    private $user = false;

    /**
     * Internal use only. Gives and instantiates an instance of the Permission Manager.
     * @return PermissionManager
     */
    protected function getPermissionManager(): PermissionManager
    {
        if (!$this->permissionManager) {
            $permissionManager = new PermissionManager($this->getGame());
        }

        return $permissionManager;
    }

    /**
     * Returns the user associated with the current auth token. If it's not a user, this method returns null.
     * @return User|null
     */
    public function getCurrentUser(): ?User
    {
        if ($this->user === false) {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();

            if (!$user instanceof User) {
                $user = null;
            }

            $this->user = $user;
        }

        return $this->user;
    }

    /**
     * Short method to check if a user is logged in or not.
     * @return bool
     */
    public function isLoggedin(): bool
    {
        return $this->getCurrentUser() === null ? false : true;
    }

    public function isAllowed($permission)
    {
        return $this->getPermissionManager()->isAllowed($this->getCurrentUser(), $permission);
    }

    public function guard($entity, array $whitelistedFields)
    {
        return new TypeGuardian($entity, $whitelistedFields);
    }
}
<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Services;

use LotGD\Core\PermissionManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\TypeGuardian;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType;
use LotGD\Crate\GraphQL\Models\User;

/**
 * Provides method to check user authorization.
 * @package LotGD\Crate\GraphQL\Services
 */
class AuthorizationService extends BaseManagerService
{
    use ContainerAwareTrait;

    /** @var PermissionManager */
    private $permissionManager;
    /** @var null|User */
    private $user = false; // false as standard value to check if it has already been set or not.

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

            // user can also be "anonymous user" and not a real one.
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
    public function isLoggedIn(): bool
    {
        return $this->getCurrentUser() === null ? false : true;
    }

    /**
     * Proxy method for PermissionManager->isAllowed
     * @see PermissionManager->isAllowed()
     * @param $permission
     * @return bool
     */
    public function isAllowed($permission)
    {
        return $this->getPermissionManager()->isAllowed($this->getCurrentUser(), $permission);
    }

    /**
     * Guards a type by adding the TypeGuardian wrapper class around it.
     * @param $type The Type to be protected
     * @param array $whitelistedFields A list of field that are accessible through TypeGuardian. Everything else will return null.
     * @return TypeGuardian
     */
    public function guard($type, array $whitelistedFields): TypeGuardian
    {
        return new TypeGuardian($type, $whitelistedFields);
    }
}
<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Services;

use LotGD\Core\PermissionManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType;
use LotGD\Crate\GraphQL\Models\User;

class AuthorizationService extends BaseManagerService
{
    use ContainerAwareTrait;

    private $permissionManager;
    private $user = false;

    protected function getPermissionManager(): PermissionManager
    {
        if (!$this->permissionManager) {
            $permissionManager = new PermissionManager($this->getGame());
        }

        return $permissionManager;
    }

    protected function getCurrentUser(): ?User
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

    public function isUser($object) {
        $user = $this->getCurrentUser();

        if (!$user) {
            return false;
        }

        if (!$object instanceof UserType) {
            return false;
        }

        if ($object->getId() === (string)$user->getId()) {
            return true;
        }

        return false;
    }

    public function isAllowed($permission) {
        return $this->getPermissionManager()->isAllowed($this->getCurrentUser(), $permission);
    }
}
<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Services;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;

use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType;
use LotGD\Crate\GraphQL\Models\User;

class AuthorizationService extends BaseManagerService
{
    use ContainerAwareTrait;

    protected function getCurrentUser(): ?User
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if ($user instanceof User) {
            return $user;
        } else {
            return null;
        }
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
}
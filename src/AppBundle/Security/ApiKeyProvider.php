<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\Security;

use Symfony\Component\DependencyInjection\ {
    ContainerAwareInterface,
    ContainerAwareTrait
};
use Symfony\Component\Security\Core\Exception\ {
    UnsupportedUserException,
    UsernameNotFoundException
};
use Symfony\Component\Security\Core\User\ {
    UserInterface,
    UserProviderInterface
};

use LotGD\Crate\GraphQL\Models\User;

/**
 * UserProvider
 */
class ApiKeyProvider implements UserProviderInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    /**
     * @inheritDoc
     */
    public function loadUserByUsername($username)
    {
        $entityManager = $this->container->get('lotgd.core.game')->getEntityManager();
        
        var_dump($username, $entityManager);
    }
    
    /**
     * @inheritDoc
     */
    public function supportsClass($class)
    {
        return $class === 'LotGD\Crate\GraphQl\Models\Account';
    }
    
    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user) {
        if (!$user instanceof Account) {
            $class = get_class($user);
            throw new UnsupportedUserException("Users of type {$class} are not supported.");
        }
        
        return $this->loadUserByUsername($user->getUsername());
    }
}

<?php

namespace LotGD\Crate\GraphQL\AppBundle\Security;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

use LotGD\Crate\GraphQL\Models\Account;

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

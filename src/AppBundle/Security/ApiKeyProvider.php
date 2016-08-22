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

use LotGD\Crate\GraphQL\Models\ApiKey;
use LotGD\Crate\GraphQL\Models\User;
use LotGD\Crate\GraphQL\Services\BaseManagerService;

/**
 * Manager for ApiKeys: Loads an user
 */
class ApiKeyProvider extends BaseManagerService implements UserProviderInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    /**
     * Returns a user matching a given api key.
     * @param string $apiKey
     * @return User|null
     */
    public function getUserForApiKey(string $apiKey)
    {
        $apiKey = $this->getEntityManager()->getRepository(ApiKey::class)
            ->findOneBy(["apiKey" => $apiKey]);
        
        return $apiKey === null ? null : $apiKey->getUser();
    }
    
    /**
     * not implemented
     */
    public function loadUserByUsername($username)
    {
    }
    
    /**
     * @inheritDoc
     */
    public function supportsClass($class)
    {
        return $class === self::class;
    }
    
    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user) {
        if (!$user instanceof ApiKey) {
            $class = get_class($user);
            throw new UnsupportedUserException("ApiKeys of type {$class} are not supported.");
        }
        
        return $this->loadUserByUsername($user->getUsername());
    }
}

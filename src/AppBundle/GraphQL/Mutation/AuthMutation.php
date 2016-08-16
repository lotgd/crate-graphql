<?php

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Mutation;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Error\UserError;

use LotGD\Crate\GraphQL\Models\User;
use LotGD\Crate\GraphQL\Models\ApiKey;

class AuthMutation implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    function authWithPassword(string $email, string $password)
    {
        $entityManager = $this->container->get('lotgd.core.game')->getEntityManager();
        
        $user = $entityManager->getRepository(User::class)
            ->findOneBy(["email" => $email]);
        $passwordVerified = false;
        
        if ($user instanceof User) {
            $passwordVerified = $user->verifyPassword($password);
        }
        
        // Do not tell if user is unknown or password wrong
        if ($user === null || $passwordVerified === false) {
            throw new UserError("The login credentials are invalid.");
        }
        
        // Generate api key
        if ($user->hasApiKey() === false) {
            $key = ApiKey::generate($user);
            $user->setApiKey($key);
        } elseif ($user->getApiKey()->isValid() === false) {
            // Delete old key
            $key = $user->getApiKey();
            $key->delete($entityManager);
            
            // Create new key
            $key = ApiKey::generate($user);
            $user->setApiKey($key);
        }
        else {
            $key = $user->getApiKey();
        }
        
        $key->setLastUsed();
        $key->save($entityManager);
        
        return [
            "apiKey" => $key->getApiKey(),
            "expiresAt" => $key->getExpiresAtAsString(),
        ];
    }
}

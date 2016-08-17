<?php

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Mutation;

use Doctrine\DBAL\DBALException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Error\UserError;

use LotGD\Crate\GraphQL\Models\User;
use LotGD\Crate\GraphQL\Models\ApiKey;
use LotGD\Crate\GraphQL\Tools\EntityManagerAwareInterface;
use LotGD\Crate\GraphQL\Tools\EntityManagerAwareTrait;

class AuthMutation implements EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;
    
    function authWithPassword(string $email = null, string $password = null)
    {
        $entityManager = $this->getEntityManager();
        
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
    
    function createPasswordUser(string $name = "", string $email = "", string $password = "")
    {
        $entityManager = $this->getEntityManager();
        
        $userByEmail = $entityManager->getRepository(User::class)
            ->findOneBy(["email" => $email]);
        if ($userByEmail !== null) {
            throw new UserError("Email address is already in use.");
        }
        
        $userByName = $entityManager->getRepository(User::class)
            ->findOneBy(["name" => $name]);
        if ($userByName !== null) {
            throw new UserError("Username is already in use.");
        }
        
        $user = new User($name, $email, $password);
        
        try {
            $user->save($entityManager);
        } catch (DBALException $ex) {
            throw new UserError("An unknown DBALException occured.");
        } catch (\Exception $ex) {
            throw new UserError("An unknown Exception occured.");
        }
        
        return [];
    }
}

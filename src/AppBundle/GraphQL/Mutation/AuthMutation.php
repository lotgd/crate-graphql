<?php

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Mutation;

use Doctrine\DBAL\DBALException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Error\UserError;

use LotGD\Crate\GraphQL\{
    Exceptions\UserEmailExistsException,
    Exceptions\UserNameExistsException,
    Models\User,
    Models\ApiKey,
    Tools\EntityManagerAwareInterface,
    Tools\EntityManagerAwareTrait
};

class AuthMutation implements EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;
    
    function authWithPassword(string $email = null, string $password = null)
    {
        $userManager = $this->container->get("lotgd.crate.graphql.user_manager");
        
        $user = $userManager->findByEmail($email);
        
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
            $oldKey = $user->getApiKey();
            $oldKey->delete($this->getEntityManager());
            unset($oldKey);
            
            // Create new key
            $newKey = ApiKey::generate($user);
            $user->setApiKey($newKey);
            
            $key = $newKey;
        }
        else {
            $key = $user->getApiKey();
        }
        
        $key->setLastUsed();
        $key->save($this->getEntityManager());
        
        return [
            "apiKey" => $key->getApiKey(),
            "expiresAt" => $key->getExpiresAtAsString(),
        ];
    }
    
    function createPasswordUser(string $name = "", string $email = "", string $password = "")
    {
        $userManager = $this->container->get("lotgd.crate.graphql.user_manager");
        
        try {
            $userManager->createNewWithPassword($name, $email, $password);
        } catch (UserNameExistsException $ex) {
            throw new UserError("Username is already in use.");
        } catch (UserEmailExistsException $ex) {
            throw new UserError("Email address is already in use.");
        } catch (\Exception $ex) {
            throw new UserError("An unknown exception occured: " . $ex->getMessage());
        }
        
        return [];
    }
}

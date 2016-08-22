<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Services;

use LotGD\Crate\GraphQL\{
    Exceptions\CrateException,
    Exceptions\UserNameExistsException,
    Exceptions\UserEmailExistsException,
    Models\User
};

/**
 * Management class for everything user account related.
 */
class UserManagerService extends BaseManagerService
{
    /**
     * Creates a new user with email/password authentification.
     * @param string $name User name
     * @param string $email User email.
     * @param string $password User password (plain)
     * @return User The created User entity
     * @throws UserNameExistsException if user name already exists.
     * @throws UserEmailExistsException if user email already exists.
     * @throws CrateException if another exception for unknown reasons occurs.
     */
    public function createNewWithPassword(
        string $name, 
        string $email, 
        string $password
    ): User {
        $entityManager = $this->getEntityManager();
        
        if ($this->findByName($name) !== null) {
            throw new UserNameExistsException();
        }
        
        if ($this->findByEmail($email) !== null) {
            throw new UserEmailExistsException();
        }
        
        $user = new User($name, $email, $password);
        
        try {
            $user->save($entityManager);
        } catch (DBALException $ex) {
            throw new CrateException("An unknown DBALException occured: " . $ex->getMessage());
        } catch (\Exception $ex) {
            throw new CrateException("An unknown Exception occured: " . $ex->getMessage());
        }
        
        return $user;
    }
    
    /**
     * Finds an user entity by name.
     * @param string $name The name to search by.
     * @return User|null The found user entity or null.
     */
    public function findByName(string $name)
    {
        return $this->getEntityManager()
            ->getRepository(User::class)
            ->findOneBy(["name" => $name]);
    }
    
    /**
     * Finds an user entity by email.
     * @param string $email The email to search by.
     * @return User|null The found user entity or null.
     */
    public function findByEmail(string $email)
    {
        return $this->getEntityManager()
            ->getRepository(User::class)
            ->findOneBy(["email" => $email]);
    }
}

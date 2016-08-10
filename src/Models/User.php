<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Models;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity
 * @Table(name="users")
 */
class User implements UserInterface
{
    /** @Id @Column(type="integer") @GeneratedValue */
    private $id;
    /** @Column(type="string", length=250, unique=True); */
    private $email;
    /** @Column(type="string", length=250); */
    private $password = "";
    
    /**
     * Constructs an user account with an email and a password.
     * @param string $email
     */
    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->setPassword($password);
    }
    
    /**
     * Returns the user id
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * Returns the user email address
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
    
    /**
     * Hashes a given plain password and stored the hash
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Verifies if a given plain password is the same as the one in the hash
     * @param string $password
     * @return bool
     */
    public function verifyPassword(string $password): bool
    {
        if (password_verify($password, $this->password)) {
            if (password_needs_rehash($this->password, PASSWORD_DEFAULT)) {
                $this->setPassword($password);
            }
            return true;
        }
        else {
            return false;
        }
    }
    
    //
    // Implementation of UserInterface
    //
    
    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        
    }
    
    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }
    
    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * @inheritDoc
     */
    public function getSalt()
    {
    }
    
    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }
}
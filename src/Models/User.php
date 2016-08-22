<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Models;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

use LotGD\Core\Models\SaveableInterface;
use LotGD\Core\Tools\Model\Saveable;

/**
 * @Entity
 * @Table(name="users")
 */
class User implements UserInterface, SaveableInterface
{
    use Saveable;
    
    /** @Id @Column(type="integer") @GeneratedValue */
    private $id;
    /** @Column(type="string", length=250, unique=True); */
    private $name;
    /** @Column(type="string", length=250, unique=True); */
    private $email;
    /** @Column(type="string", length=250); */
    private $passwordHash = "";
    /** @OneToOne(targetEntity="ApiKey", mappedBy="user", cascade={"persist"}) */
    private $apiKey;
    
    /**
     * Constructs an user account with an email and a password.
     * @param string $email Email address
     * @param string $password plain text password to be hashed
     */
    public function __construct(string $name, string $email, string $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->setPassword($password);
    }
    
    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * Returns the user email address
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
     * @inheritDoc
     */
    public function setPassword(string $password)
    {
        $this->passwordHash = password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * @inheritDoc
     */
    public function verifyPassword(string $password): bool
    {
        if (password_verify($password, $this->passwordHash)) {
            if (password_needs_rehash($this->passwordHash, PASSWORD_DEFAULT)) {
                $this->setPassword($password);
            }
            return true;
        }
        else {
            return false;
        }
    }
    
    /**
     * Sets an api key to belong to this user.
     * @param ApiKey $key
     */
    public function setApiKey(ApiKey $key)
    {
        $this->apiKey = $key;
    }
    
    /**
     * Returns true if a user has an api key.
     * @return bool
     */
    public function hasApiKey(): bool
    {
        return !is_null($this->apiKey);
    }
    
    /**
     * Returns the api key instance.
     * @return ApiKey
     */
    public function getApiKey(): ApiKey
    {
        return $this->apiKey;
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
    public function eraseCredentials()
    {
    }
    
    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->getName();
    }
    
    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return "NaCl";
    }
    
    /**
     * Returns the hashed password (including salt)
     * @return type
     */
    public function getPassword()
    {
        return $this->passwordHash;
    }
}
<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Models;

use Generator;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\Common\Collections\ArrayCollection;

use LotGD\Core\Models\Actor;
use LotGD\Core\Models\SaveableInterface;
use LotGD\Core\Tools\Model\Saveable;
use LotGD\Core\Models\Character;

/**
 * @Entity
 * @Table(name="users")
 */
class User extends Actor implements UserInterface, SaveableInterface
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
    /** @OneToMany(targetEntity="UserPermissionAssociation", mappedBy="owner", cascade={"persist", "remove"}, orphanRemoval=true) */
    protected $permissions;
    /**
     * Unidirectional OneToMany association since we cannot modify the character
     * model from the core. Instead, we use a join table to list all characters
     * associated to an user.
     * @ManyToMany(targetEntity="LotGD\Core\Models\Character", cascade={"persist"})
     * @JoinTable("users_characters",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="character_id", referencedColumnName="id", unique=true)}
     * )
     */
    private $characters;

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
        $this->characters = new ArrayCollection();
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

    /**
     * Iterates through all characters.
     * @return Collection|null
     */
    public function getCharacters()
    {
        return $this->characters;
    }

    /**
     * Iterates through all characters.
     * @return \Generator
     */
    public function fetchCharacters(): \Generator
    {
        foreach ($this->characters as $character) {
            yield $character;
        }
    }

    /**
     * Returns true if the user has the passed character.
     * @param Character $character
     * @return bool
     */
    public function hasCharacter(Character $character): bool
    {
        return $this->characters->contains($character);
    }

    /**
     * Adds a character to this user.
     * @param Character $character
     */
    public function addCharacter(Character $character)
    {
        if ($this->hasCharacter($character) === false) {
            $this->characters->add($character);
        }
    }

    /**
     * Returns FQCN of the Permission association class
     * @return string
     */
    protected function getPermissionAssociationClass(): string
    {
        return UserPermissionAssociation::class;
    }

    /**
     * Iterates through permissions.
     * @return Generator
     */
    protected function getPermissionAssociations(): Generator
    {
        foreach ($this->permissions as $permission) {
            yield $permission;
        }
    }
}
<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use Doctrine\Common\Collections\Collection;

use LotGD\Core\Game;
use LotGD\Crate\GraphQL\Models\User;

/**
 * Representation of the GraphQL "User" type
 */
class UserType extends BaseType
{
    /** @var User the user instance */
    private $userEntity;

    /**
     * @param Game $game Game object
     * @param User $userEntity User entity
     */
    public function __construct(Game $game, User $userEntity = null)
    {
        parent::__construct($game);
        $this->userEntity = $userEntity;
    }

    /**
     * Returns the user id.
     * @return string
     */
    public function getId(): string
    {
        return (string)$this->userEntity->getId();
    }

    /**
     * Returns the user name.
     * @return string
     */
    public function getName(): string
    {
        return $this->userEntity->getName();
    }

    /**
     * Returns the character collection.
     * @return Collection
     */
    public function getCharacters(): Collection
    {
        return $this->userEntity->getCharacters();
    }
}

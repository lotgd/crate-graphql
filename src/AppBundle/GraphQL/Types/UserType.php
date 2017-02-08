<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;
use LotGD\Crate\GraphQL\Models\User;

/**
 * Representation of the GraphQL "User" type
 */
class UserType extends BaseType
{
    /** @var User the user instance */
<<<<<<< 403c98b757cd5e5a2ed68c71e6c6b99ab7823948
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
     * Returns a UserType for an user with a given id.
     * @param Game $game
     * @param int $userId
     * @return type
     */
    public static function fromId(Game $game, int $userId)
    {
        $em = $game->getEntityManager();
        $user = $em->getRepository(User::class)->find($userId);

        return ($user ? new static($game, $user) : null);
    }

    /**
     * Returns a UserType with for an user with a given name.
     * @param Game $game
     * @param string $userName
     * @return type
     */
    public static function fromName(Game $game, string $userName)
    {
        $em = $game->getEntityManager();
        $user = $em->getRepository(User::class)->findOneBy(["name" => $userName]);

        return ($user ? new static($game, $user) : null);
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
        return (string)$this->userEntity->getName();
    }

    public function getCharacters()
    {
        return $this->_user->getCharacters();
    }
}

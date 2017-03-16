<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Mutation;

use LotGD\Core\PermissionManager;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Error\UserError;

use LotGD\Crate\GraphQL\AppBundle\GraphQL\Connections\CharacterConnection;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\CharacterType;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType;
use LotGD\Crate\GraphQL\Services\BaseManagerService;
use LotGD\Crate\GraphQL\Tools\EntityManagerAwareInterface;
use LotGD\Crate\GraphQL\Tools\EntityManagerAwareTrait;
use LotGD\Crate\GraphQL\Tools\ManagerAwareTrait;

use LotGD\Crate\GraphQL\Exceptions\CharacterNameExistsException;


/**
 * Resolver for taking an action
 */
class CharacterMutation extends BaseManagerService implements EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;
    use ManagerAwareTrait;

    /**
     * createCharacterMutation resolver - creates a character for a given user and a given name.
     * @param string $userId Owner user id
     * @param string $characterName Owner character name
     * @return graphql createCharacterMutationPayload
     * @throws UserError
     */
    function createCharacter(string $userId, string $characterName)
    {
        // check if user is not logged in
        if (!$this->getAuthorizationService()->isLoggedin()) {
            throw new UserError("Access denied for this mutation.");
        }

        // Get the user given in the arguments
        $user = $this->getUserManager()->findById((int)$userId);

        // Check if the user is the current user - or the current user is superuser. If not, deny access.
        if (
            $user !== $this->getAuthorizationService()->getCurrentUser() and
            $this->getAuthorizationService()->isAllowed(PermissionManager::Superuser) === false
        ) {
            throw new UserError("Access denied for this mutation.");
        }

        /** @ToDo: Add check for amount of characters this user has. */
        try {
            $character = $this->getCharacterManager()->createNewCharacter($characterName);
            $user->addCharacter($character);
        } catch(CharacterNameExistsException $e) {
            throw new UserError($e->getMessage());
        }

        $this->game->getEntityManager()->flush();

        return [
            "character" => new CharacterType($this->getGame(), $character),
            "user" => new UserType($this->getGame(), $user),
        ];
    }

    function takeAction($characterId, $actionId)
    {
        // check if user is not logged in
        if (!$this->getAuthorizationService()->isLoggedin()) {
            throw new UserError("Access denied for this mutation.");
        }

        $character = $this->getCharacterManager()->findById($characterId);

        // Return null if character has not been found.
        if (is_null($character)) {
            return null;
        }

        // Check if the user own the character or the current user is superuser. If not, deny access.
        if (
            $this->getAuthorizationService()->getCurrentUser()->hasCharacter($character) === false and
            $this->getAuthorizationService()->isAllowed(PermissionManager::Superuser) === false
        ) {
            throw new UserError("Access denied for this mutation.");
        }

        $game = $this->getGame();
        $game->setCharacter($character);

        $game->takeAction($actionId);

        $argument = new Argument([
            "characterId" => $characterId,
        ]);

        $a = $this->container->get("app.graph.resolver.viewpoint")->resolve($argument);
        return ["viewpoint" => $a];
    }
}

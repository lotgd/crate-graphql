<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Mutation;

use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Error\UserError;

use LotGD\Crate\GraphQL\AppBundle\GraphQL\Connections\CharacterConnection;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\CharacterType;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType;
use LotGD\Crate\GraphQL\Services\BaseManagerService;
use LotGD\Crate\GraphQL\Tools\EntityManagerAwareInterface;
use LotGD\Crate\GraphQL\Tools\EntityManagerAwareTrait;

use LotGD\Crate\GraphQL\Exceptions\CharacterNameExistsException;


/**
 * Resolver for taking an action
 */
class CharacterMutation extends BaseManagerService implements EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;

    /**
     * createCharacterMutation resolver - creates a character for a given user and a given name.
     * @param string $userId Owner user id
     * @param string $characterName Owner character name
     * @return graphql createCharacterMutationPayload
     * @throws UserError
     */
    function createCharacter(string $userId, string $characterName)
    {
        // Get user
        $user = $this->container->get("lotgd.crate.graphql.user_manager")->findById((int)$userId);

        /** @ToDo: Add check for amount of characters this user has. */
        try {
            $character = $this->container->get("lotgd.crate.graphql.character_manager")->createNewCharacter($characterName);
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
        $character = $this->container->get("lotgd.crate.graphql.character_manager")->findById($characterId);

        // Return null if character has not been found.
        if (is_null($character)) {
            return null;
        }

        // @ToDo: Access restriction.
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

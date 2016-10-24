<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Mutation;

use LotGD\Crate\GraphQL\Services\BaseManagerService;
use LotGD\Crate\GraphQL\Tools\EntityManagerAwareInterface;
use LotGD\Crate\GraphQL\Tools\EntityManagerAwareTrait;

/**
 * Resolver for taking an action
 */
class ViewpointMutation extends BaseManagerService implements EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;
    
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
        
        return $this->container->get("app.graph.resolver.viewpoint")->resolve($argument);
    }
}

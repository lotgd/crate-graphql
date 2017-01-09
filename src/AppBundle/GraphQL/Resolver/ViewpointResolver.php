<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;

use LotGD\Core\Exceptions\InvalidConfigurationException;
use LotGD\Core\Models\Scene;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\ViewpointType;
use LotGD\Crate\GraphQL\Services\BaseManagerService;

class ViewpointResolver extends BaseManagerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function resolve(Argument $args = null)
    {
        // @ToDo: Implement getting selected character from user session (see SessionResolver).
        if (empty($args["characterId"])) {
            return null;
        }
        
        /* @var LotGD\Core\Models\Character */
        $character = $this->container->get("lotgd.crate.graphql.character_manager")->findById((int)$args["characterId"]);
        
        if ($character) {            
            // @ToDo Return null if user has no access rights to this character.
            $game = $this->getGame();
            $game->setCharacter($character);
            
            try {
                $viewpointType = new ViewpointType($this->getGame(), $game->getViewpoint());
                
                return $viewpointType;
            } catch (InvalidConfigurationException $e) {
                throw new \Overblog\GraphQLBundle\Error\UserError("No default scene handler found.");
            }
            
            return null;
        } else {
            return null;
        }
    }
}

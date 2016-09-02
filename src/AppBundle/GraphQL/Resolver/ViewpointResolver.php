<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;

use LotGD\Core\Exceptions\InvalidConfigurationException;
use LotGD\Crate\GraphQL\Services\BaseManagerService;

class ViewpointResolver extends BaseManagerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function resolve(Argument $args = null)
    {
        if (empty($args["characterId"])) {
            return null;
        }

        $characterId = (int)$args["characterId"];
        /* @var LotGD\Core\Models\Character */
        $character = $this->container->get("lotgd.crate.graphql.character_manager")->findById($characterId);

        if (is_null($character)) {
            // Character not found, return null
            return null;
        } else {
            // @ToDo Return null if user has no access rights to this character.
            $game = $this->getGame();
            $game->setCharacter($character);

            try {
                $viewpoint = $game->getViewpoint();

                return [
                    "title" => "Hallo",
                    "description" => "Hallo.",
                ];
            } catch (InvalidConfigurationException $e) {
                throw new \Overblog\GraphQLBundle\Error\UserError("No default scene!");
            }
        }
    }
}

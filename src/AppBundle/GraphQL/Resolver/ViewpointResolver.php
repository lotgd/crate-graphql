<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;

use LotGD\Core\Exceptions\InvalidConfigurationException;
use LotGD\Core\Models\Scene;
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
                $viewpointActionGroups = $viewpoint->getActionGroups();
                $actionGroups = [];

                foreach ($viewpointActionGroups as $group) {
                    $groupActions = $group->getActions();
                    $actions = [];

                    foreach ($groupActions as $action) {
                        $actions[] = [
                            "id" => $action->getId(),
                            "title" => $game->getEntityManager()->getRepository(Scene::class)
                                ->find($action->getDestinationSceneId())->getTitle(),
                        ];
                    }

                    $actionGroups[] = [
                        "id" => $group->getId(),
                        "title" => $group->getTitle(),
                        "sortKey" => $group->getSortKey(),
                        "actions" => $actions,
                    ];
                }

                return [
                    "title" => $viewpoint->getTitle(),
                    "description" => $viewpoint->getDescription(),
                    "template" => $viewpoint->getTemplate(),
                    "actionGroups" => $actionGroups,
                ];
            } catch (InvalidConfigurationException $e) {
                throw new \Overblog\GraphQLBundle\Error\UserError("No default scene handler found.");
            }
        }
    }
}

<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Error\UserError;

use LotGD\Core\PermissionManager;
use LotGD\Core\Exceptions\InvalidConfigurationException;
use LotGD\Core\Models\Scene;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\ViewpointType;
use LotGD\Crate\GraphQL\Services\BaseManagerService;
use LotGD\Crate\GraphQL\Tools\ManagerAwareTrait;
use LotGD\ModuleForms\Form;

class AttachmentResolver extends BaseManagerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ManagerAwareTrait;

    /**
     * @var TypeResolver
     */
     private $typeResolver;

     public function __construct(TypeResolver $typeResolver)
     {
         $this->typeResolver = $typeResolver;
     }

     public function resolveType($data)
     {
         $formType = $this->typeResolver->resolve('Form');

         if ($data->getType() == Form:AttachmentType) {
             return $formType;
         }
         return null;
     }

    /**
     * Resolves the form attachment.
     * @param Argument $args
     * @return AttachmentType
     */
    public function resolveForm(Argument $args = null)
    {
        if (empty($args["id"])) {
            return null;
        }

        /** @var LotGD\Core\Models\Character */
        $character = $this->getCharacterManager()->findById((int)$args["characterId"]);

        if ($character) {
            // If currentUser does not own the character and if he isn't a superuser, throw UserError
            if (
                $this->getAuthorizationService()->getCurrentUser()->hasCharacter($character) === false and
                $this->getAuthorizationService()->isAllowed(PermissionManager::Superuser) === false
            ) {
                throw new UserError("Access denied.");
            }

            $game = $this->getGame();
            $game->setCharacter($character);

            try {
                $viewpointType = new ViewpointType($this->getGame(), $game->getViewpoint());

                return $viewpointType;
            } catch (InvalidConfigurationException $e) {
                throw new UserError("No default scene handler found.");
            }
        } else {
            return null;
        }
    }
}

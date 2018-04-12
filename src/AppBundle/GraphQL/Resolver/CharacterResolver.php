<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver;

use Doctrine\Common\Util\Debug;
use LotGD\Core\Models\Permission;
use LotGD\Core\PermissionManager;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\CharacterStatType;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Error\UserError;

use LotGD\Core\Models\Character;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Connections\CharacterConnection;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\CharacterType;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\TypeInterface;
use LotGD\Crate\GraphQL\Services\BaseManagerService;
use LotGD\Crate\GraphQL\Tools\ManagerAwareTrait;

class CharacterResolver extends BaseManagerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ManagerAwareTrait;

    public function resolve(Argument $args = null)
    {
        $characterEntity = null;

        if (isset($args["characterId"])) {
            $characterEntity = $this->getCharacterManager()
                ->findById((int)$args["characterId"]);
        } elseif (isset($args["characterName"])) {
            $characterEntity = $this->getCharacterManager()
                ->findByName($args["characterName"]);
        }

        // Check if current user has access rights to this character (owner or superuser)
        $authService = $this->getAuthorizationService();

        if (
            $authService->isLoggedin() and (
                $authService->getCurrentUser()->hasCharacter($characterEntity) or
                $authService->isAllowed(PermissionManager::Superuser)
            )
        ) {
            $callback = function($e) { return $e; };
        } else {
            // The character does not belong to the current user - we must protect the sensitive data.
            $callback = function($e) {
                return $this->getAuthorizationService()->guard(
                    $e,
                    ["id", "name", "displayName", "level", "attack", "defense", "health", "maxHealth", "publicStats"]
                );
            };
        }

        return $this->resolveFromEntity($characterEntity, $callback);
    }

    public function resolveFromEntity(?Character $characterEntity, callable $protection): ?TypeInterface
    {
        if ($characterEntity !== null) {
            return $protection(new CharacterType($this->getGame(), $characterEntity));
        } else {
            return null;
        }
    }

    public function resolveCharacterStat(CharacterStatType $type)
    {
        return $type->getType();
    }
}
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

class FighterResolver extends BaseManagerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ManagerAwareTrait;

    public function resolveType($value)
    {
        return $value->getType();
    }
}
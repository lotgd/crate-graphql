<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;

use LotGD\Crate\GraphQL\Services\BaseManagerService;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\RealmType;

class RealmResolver extends BaseManagerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function resolveType($type)
    {
        // Resolve the type for the object Type Realm (as defined in config/graphql/Realm.types.yml)
        $typeResolver = $this->container->get('overblog_graphql.type_resolver');
        return $typeResolver->resolve("Realm");
    }

    public function resolve(Argument $args = null)
    {
        $moduleManager = $this->container->get('lotgd.core.game')->getModuleManager();
        $modules = $moduleManager->getModules();
        
        return new RealmType($this->container->get("lotgd.core.game")->getGame());
    }
}

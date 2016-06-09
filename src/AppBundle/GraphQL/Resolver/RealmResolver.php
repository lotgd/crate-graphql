<?php

namespace LotGD\Crate\WWW\AppBundle\GraphQL\Resolver;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;

class RealmResolver implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    public function resolveType($type)
    {
        // Resolve the type for the object Type Realm (as defined in config/graphql/Realm.types.yml)
        $typeResolver = $this->container->get('overblog_graphql.type_resolver');
        return $typeResolver->resolve("Realm");
    }
    
    public function resolveRealm(Argument $args = null)
    {
        // Realm is defined as an object, but arrays work too
        return [
            "name" => "Test-Environment",
            "crateVersion" => "0.1.0-dev",
            "coreVersion" => $this->container->get("lotgd.core.game")->getVersion()
        ];
    }
}

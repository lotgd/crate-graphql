<?php

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;

use LotGD\Crate\GraphQL\Services\BaseManagerService;
use LotGD\Crate\GraphQL\Models\User;


class SessionResolver extends BaseManagerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function resolve(Argument $args = null)
    {
        $return = [
            "user" => $this->container->get('security.token_storage')->getToken()->getUser(),
            "apiKey" => null,
            "expiresAt" => null
        ];
        
        if ($return["user"] instanceof User) { 
            // User must have an api key or else he would not be authenticated.
            $apiKey = $return["user"]->getApiKey();
            
            $return["apiKey"] = $apiKey->getApiKey();
            $return["expiresAt"] = $apiKey->getExpiresAtAsString();
            
            $userResolver = $this->container->get('app.graph.resolver.user');
            $argument = new Argument([
                "name" => $return["user"]->getName()
            ]);
            
            $return["user"] = $userResolver->resolve($argument);
            
            return $return;
        }
        else {
            if (isset($args["apiKey"])) {
                $em = $this->getEntityManager();
                $apiKey = $em->getRepository(\LotGD\Crate\GraphQL\Models\ApiKey::class)
                    ->findOneBy(["apiKey" => $args["apiKey"]]);
                
                if ($apiKey !== null) {
                    $user = $apiKey->getUser();
                    
                    $userResolver = $this->container->get('app.graph.resolver.user');
                    $argument = new Argument([
                        "name" => $user->getName()
                    ]);
                    
                    return [
                        "user" => $userResolver->resolve($argument),
                        "apiKey" => $apiKey->getApiKey(),
                        "expiresAt" => $apiKey->getExpiresAtAsString()
                    ];
                }
            }
            
            return [
                "user" => null,
                "apiKey" => null,
                "expiresAt" => null
            ];
        }
    }
}

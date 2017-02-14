<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;

use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\SessionType;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType;
use LotGD\Crate\GraphQL\Models\User;
use LotGD\Crate\GraphQL\Models\ApiKey;
use LotGD\Crate\GraphQL\Services\BaseManagerService;


class SessionResolver extends BaseManagerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function resolve(Argument $args = null)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $sessionType = new SessionType($this->getGame());

        if ($user instanceof User) {
            $sessionType->setApiKey($user->getApiKey()->getApiKey());
            $sessionType->setExpiresAt($user->getApiKey()->getExpiresAtAsString());
            $sessionType->setUser(new UserType($this->getGame(), $user));
        } elseif(isset($args["apiKey"])) {
            $apiKey = $this->getEntityManager()
                ->getRepository(ApiKey::class)
                ->findOneBy(["apiKey" => $args["apiKey"]]);

            if ($apiKey !== null) {
                $user = $apiKey->getUser();

                $sessionType->setUser(new UserType($this->getGame(), $user));
                $sessionType->setApiKey($apiKey->getApiKey());
                $sessionType->setExpiresAt($apiKey->getExpiresAtAsString());
            }
        }

        return $sessionType;
    }
}

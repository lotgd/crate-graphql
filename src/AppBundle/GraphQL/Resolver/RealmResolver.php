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

    public function resolve(): RealmType
    {
        return new RealmType($this->container->get("lotgd.core.game")->getGame());
    }
}

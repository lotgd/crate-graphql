<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests\Resolver;

use LotGD\Crate\GraphQL\Tests\WebTestCase;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver\RealmResolver;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\RealmType;

class RealmResolverTest extends WebTestCase
{
    protected function getResolver()
    {
        $resolver = new RealmResolver();
        $this->startupService($resolver);

        return $resolver;
    }

    public function testIfRealmResolverReturnsRealmType()
    {
        $type = $this->getResolver()->resolve();
        $this->assertNotNull($type);
        $this->assertInstanceOf(RealmType::class, $type);
    }
}

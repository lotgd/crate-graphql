<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests\Resolver;

use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Error\UserError;
use Symfony\Component\DependencyInjection\ContainerInterface;

use LotGD\Crate\GraphQL\Services\AuthorizationService;
use LotGD\Crate\GraphQL\Tests\WebTestCase;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver\UserResolver;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType;

class UserResolverTest extends WebTestCase
{
    protected function getResolver()
    {
        $resolver = new UserResolver();
        $authServiceMock = $this->createMock(AuthorizationService::class);
        $authServiceMock->method("isLoggedin")->willReturn(true);
        $authServiceMock->method("isAllowed")->willReturn(true);
        $authServiceMock->method("getCurrentUser")->willReturn(null);

        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock->method("get")->willReturnCallback(function($serviceName) use ($authServiceMock) {
            if ($serviceName === "lotgd.authorization") {
                return $authServiceMock;
            } else {
                return static::$kernel->getContainer()->get($serviceName);
            }
        });

        $this->startupService($resolver, null, $containerMock);

        return $resolver;
    }

    public function testIfUserResolverReturnsNullWithoutArguments()
    {
        $this->expectException(UserError::class);
        $type = $this->getResolver()->resolve();

        $this->assertNull($type);
    }

    public function testIfUserResolverReturnsUserIfGivenCorrectId()
    {
        $args = $this->getMockedArgument(["id" => "1"]);
        $type = $this->getResolver()->resolve($args);

        $this->assertNotNull($type);
        $this->assertInstanceOf(UserType::class, $type);
        $this->assertEquals("1", $type->getId());
    }

    public function testIfUserResolverReturnsNullIfGivenIncorrectId()
    {
        $args = $this->getMockedArgument(["id" => "20170306040000"]);
        $type = $this->getResolver()->resolve($args);

        $this->assertNull($type);
    }

    public function testIfUserResolverReturnsUserIfGivenCorrectName()
    {
        $args = $this->getMockedArgument(["name" => "admin"]);
        $type = $this->getResolver()->resolve($args);

        $this->assertNotNull($type);
        $this->assertInstanceOf(UserType::class, $type);
        $this->assertEquals("admin", $type->getName());
    }

    public function testIFUserResolverReturnsNullIfGivenIncorrectName()
    {
        $args = $this->getMockedArgument(["name" => "Not the admin"]);
        $type = $this->getResolver()->resolve($args);

        $this->assertNull($type);
    }
}

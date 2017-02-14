<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests\Resolver;

use Overblog\GraphQLBundle\Definition\Argument;

use LotGD\Crate\GraphQL\Tests\WebTestCase;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver\UserResolver;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType;

class UserResolverTest extends WebTestCase
{
    protected function getResolver()
    {
        $resolver = new UserResolver();
        $this->startupService($resolver);

        return $resolver;
    }

    protected function getMockedArgument(array $arguments): Argument
    {
        $args = $this->createMock(Argument::class);
        $args->method("offsetGet")->will($this->returnCallback(
            function ($key) use ($arguments) {
                return $arguments[$key];
            }
        ));
        $args->method("offsetExists")->will($this->returnCallback(
            function ($key) use ($arguments) {
                return isset($arguments[$key]);
            }
        ));

        return $args;
    }

    public function testIfUserResolverReturnsNullWithoutArguments()
    {
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

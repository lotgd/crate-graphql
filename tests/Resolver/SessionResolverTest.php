<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests\Resolver;

use Symfony\Component\DependencyInjection\Container;
use Overblog\GraphQLBundle\Definition\Argument;

use LotGD\Crate\GraphQL\Models\ApiKey;
use LotGD\Crate\GraphQL\Models\User;
use LotGD\Crate\GraphQL\Tests\WebTestCase;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver\SessionResolver;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\SessionType;

class SessionResolverTest extends WebTestCase
{
    protected function getResolver($userEntity = null)
    {
        $resolver = new SessionResolver();
        $containerMock = $this->createMock(Container::class);
        $containerMock->method("get")->will($this->returnCallback(function ($service) use ($userEntity) {
            if ($service === "security.token_storage") {
                return new class($userEntity) {
                    private $userEntity;

                    public function __construct($userEntity) {
                        $this->userEntity = $userEntity;
                    }

                    public function getToken() {
                        return new class($this->userEntity) {
                            public function __construct($userEntity) {
                                $this->userEntity = $userEntity;
                            }

                            public function getUser() {
                                return $this->userEntity;
                            }
                        };
                    }
                };
            }
        }));
        $this->startupService($resolver, null, $containerMock);

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

    public function testIfSessionResolverReturnsSessionTypeWithNullFieldsIfNeitherUserNorApiKeyAreSupplied()
    {
        $resolver = $this->getResolver(null);
        $type = $resolver->resolve();

        $this->assertInstanceOf(SessionType::class, $type);
        $this->assertNull($type->getUser());
        $this->assertNull($type->getApiKey());
        $this->assertNull($type->getExpiresAt());
    }

    public function testIfSessionResolverReturnsSessionWithUserEntityIfTokenStorageServiceHasUserStored()
    {
        $apiKeyMock = $this->createMock(ApiKey::class);
        $apiKeyMock->method("getApiKey")->willReturn("apiKey");
        $apiKeyMock->method("getExpiresAtAsString")->willReturn("Expires In 2017");
        $userEntity = $this->createMock(User::class);
        $userEntity->method("getId")->willReturn(12345);
        $userEntity->method("getApiKey")->willReturn($apiKeyMock);

        $resolver = $this->getResolver($userEntity);
        $type = $resolver->resolve();

        $this->assertInstanceOf(SessionType::class, $type);
        $this->assertEquals("apiKey", $type->getApiKey());
        $this->assertEquals("Expires In 2017", $type->getExpiresAt());
        $this->assertEquals(12345, $type->getUser()->getId());
    }

    public function testIfSessionResolverReturnsSessionWithUserEntityIfApiKeyIsProvided()
    {
        $args = $this->getMockedArgument(["apiKey" => "c4fEAJLQlaV/47UZl52nAQ=="]);

        $resolver = new SessionResolver();
        $this->startupService($resolver);
        $type = $resolver->resolve($args);

        $this->assertInstanceOf(SessionType::class, $type);
        $this->assertEquals("c4fEAJLQlaV/47UZl52nAQ==", $type->getApiKey());
        $this->assertEquals("2999-12-31T23:59:59+00:00", $type->getExpiresAt());
        $this->assertEquals(2, $type->getUser()->getId());
    }
}

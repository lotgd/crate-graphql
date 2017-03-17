<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests\Resolver;

use LotGD\Crate\GraphQL\Models\User;
use LotGD\Crate\GraphQL\Services\CharacterManagerService;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Error\UserError;
use Symfony\Component\DependencyInjection\Container;

use LotGD\Core\Game;
use LotGD\Core\Models\Character;
use LotGD\Core\Models\Viewpoint;
use LotGD\Core\Exceptions\InvalidConfigurationException;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver\ViewpointResolver;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\ViewpointType;
use LotGD\Crate\GraphQL\Tests\WebTestCase;
use LotGD\Crate\GraphQL\Services\CoreGameService;
use LotGD\Crate\GraphQL\Services\AuthorizationService;

class ViewpointResolverTest extends WebTestCase
{
    protected function getResolver(Character $characterEntity = null, CoreGameService $game = null)
    {
        $resolver = new ViewpointResolver();

        $userMock = $this->createMock(User::class);
        $userMock->method("hasCharacter")->willReturn("true");

        $authServiceMock = $this->createMock(AuthorizationService::class);
        $authServiceMock->method("isLoggedin")->willReturn(true);
        $authServiceMock->method("isAllowed")->willReturn(true);
        $authServiceMock->method("getCurrentUser")->willReturn($userMock);

        $characterServiceMock = $this->createMock(CharacterManagerService::class);
        $characterServiceMock->method("findById")->willReturn($characterEntity);


        $containerMock = $this->createMock(Container::class);
        $containerMock->method("get")->will($this->returnCallback(function ($service) use ($characterServiceMock, $authServiceMock) {
            if ($service === "lotgd.crate.graphql.character_manager") {
                return $characterServiceMock;
            } elseif ($service === "lotgd.authorization") {
                return $authServiceMock;
            } else {
                return static::$kernel->getContainer()->get($service);
            }
        }));
        $this->startupService($resolver, $game, $containerMock);

        return $resolver;
    }

    public function testIfViewpointResolverReturnsNullIfCharacterIdIsNotGiven()
    {
        $args = $this->getMockedArgument([]);
        $resolver = $this->getResolver();

        $type = $resolver->resolve($args);
        $this->assertNull($type);
    }

    public function testIfViewpointResolverReturnsNullIfCharacterWasNotFound()
    {
        $args = $this->getMockedArgument(["characterId" => "1"]);
        $resolver = $this->getResolver();

        $type = $resolver->resolve($args);
        $this->assertNull($type);

    }

    public function testIfViewpointResolverReturnsViewpointIfCharacterWasFound()
    {
        $args = $this->getMockedArgument(["characterId" => "1"]);
        $characterEntity = $this->createMock(Character::class);
        $characterViewpoint = $this->createMock(Viewpoint::class);
        $game = $this->createMock(Game::class);
        $game->method("getViewpoint")->willReturn($characterViewpoint);
        $gameService = $this->createMock(CoreGameService::class);
        $gameService->method("getGame")->willReturn($game);
        $resolver = $this->getResolver($characterEntity, $gameService);

        $type = $resolver->resolve($args);
        $this->assertNotNull($type);
        $this->assertInstanceOf(ViewpointType::Class, $type);
    }

    public function testIfViewpointResolverThrowsUserErrorIfGameThrowsInvalidConfigurationError()
    {
        $args = $this->getMockedArgument(["characterId" => "1"]);
        $characterEntity = $this->createMock(Character::class);
        $characterViewpoint = $this->createMock(Viewpoint::class);
        $game = $this->createMock(Game::class);
        $game->method("getViewpoint")->will($this->returnCallback(function() {
            throw new InvalidConfigurationException();
        }));
        $gameService = $this->createMock(CoreGameService::class);
        $gameService->method("getGame")->willReturn($game);
        $resolver = $this->getResolver($characterEntity, $gameService);

        $this->expectException(UserError::class);
        $type = $resolver->resolve($args);
    }
}

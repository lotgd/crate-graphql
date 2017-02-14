<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests\Resolver;

use Doctrine\Common\Collections\Collection;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Error\UserError;

use LotGD\Core\Models\Character;
use LotGD\Crate\GraphQL\Tests\WebTestCase;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Connections\CharacterConnection;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Resolver\CharacterResolver;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\CharacterType;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType;

class CharacterResolverTest extends WebTestCase
{
    protected function getResolver()
    {
        $resolver = new CharacterResolver();
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

    public function testIfCharacterResolverWithoutArgumentsReturnsNull()
    {
        $resolver = $this->getResolver();

        $return = $resolver->resolve($this->getMockedArgument([]));
        $this->assertNull($return);
    }

    public function testIfCharacterResolverWithIdReturnsCorrectCharacterType()
    {
        $resolver = $this->getResolver();

        $return = $resolver->resolve($this->getMockedArgument(["characterId" => "1"]));
        $this->assertNotNull($return);
        $this->assertInstanceOf(CharacterType::class, $return);
        $this->assertSame("1", $return->getId());
    }

    public function testIfCharacterResolverWithIncorrectIdReturnsNull()
    {
        $resolver = $this->getResolver();

        $return = $resolver->resolve($this->getMockedArgument(["characterId" => "19812371293"]));
        $this->assertNull($return);
    }

    public function testIfCharacterResolverWithNameReturnsCorrectCharacterType()
    {
        $resolver = $this->getResolver();

        $return = $resolver->resolve($this->getMockedArgument(["characterName" => "One"]));
        $this->assertNotNull($return);
        $this->assertInstanceOf(CharacterType::class, $return);
        $this->assertSame("One", $return->getName());
    }

    public function testIfCharacterResolverWithIncorrectNameReturnsNull()
    {
        $resolver = $this->getResolver();

        $return = $resolver->resolve($this->getMockedArgument(["characterName" => "None"]));
        $this->assertNull($return);
    }

    public function testIfGetCharacterConnectionForUserReturnsACharacterConnection()
    {
        $resolver = $this->getResolver();
        $collection = $this->createMock(Collection::class);
        $userType = $this->createMock(UserType::class);
        $userType->method("getCharacters")->willReturn($collection);
        $args = $this->getMockedArgument(["first" => 2]);

        $connection = $resolver->getCharacterConnectionForUser($userType, $args);
        $this->assertNotNull($connection);
        $this->assertInstanceOf(CharacterConnection::class, $connection);
    }

    public function testIfGetCharacterFromCursorReturnsACharacterIfValidAndSensefulCursorHasBeenGiven()
    {
        $resolver = $this->getResolver();
        $collection = $this->createMock(Collection::class);
        $userType = $this->createMock(UserType::class);
        $userType->method("getCharacters")->willReturn($collection);
        $characterTypes = [
            $this->createMock(Character::class),
            $this->createMock(Character::class),
            $this->createMock(Character::class),
        ];
        for ($i = 1; $i <= count($characterTypes); $i++) {
            $characterTypes[$i-1]->method("getId")->willReturn($i);
        }
        $collection->method("slice")->will($this->returnCallback(function($offset, $limit) use ($characterTypes) {
            return array_slice($characterTypes, $offset, $limit);
        }));

        $type = $resolver->getCharacterFromCursor(["cursor" => CharacterConnection::encodeCursor(0), "__data" => $userType]);
        $this->assertInstanceOf(CharacterType::class, $type);
        $this->assertEquals($characterTypes[0]->getId(), $type->getId());

        $type = $resolver->getCharacterFromCursor(["cursor" => CharacterConnection::encodeCursor(1), "__data" => $userType]);
        $this->assertInstanceOf(CharacterType::class, $type);
        $this->assertEquals($characterTypes[1]->getId(), $type->getId());

        $type = $resolver->getCharacterFromCursor(["cursor" => CharacterConnection::encodeCursor(2), "__data" => $userType]);
        $this->assertInstanceOf(CharacterType::class, $type);
        $this->assertEquals($characterTypes[2]->getId(), $type->getId());
    }

    public function testIfGetCharacterFromCursorReturnsNullIfValidButMeaninglessCursorHasBeenGiven()
    {
        $resolver = $this->getResolver();
        $collection = $this->createMock(Collection::class);
        $userType = $this->createMock(UserType::class);
        $userType->method("getCharacters")->willReturn($collection);
        $characterTypes = [
            $this->createMock(Character::class),
            $this->createMock(Character::class),
            $this->createMock(Character::class),
        ];
        for ($i = 1; $i <= count($characterTypes); $i++) {
            $characterTypes[$i-1]->method("getId")->willReturn($i);
        }
        $collection->method("slice")->will($this->returnCallback(function($offset, $limit) use ($characterTypes) {
            return array_slice($characterTypes, $offset, $limit);
        }));

        $type = $resolver->getCharacterFromCursor(["cursor" => CharacterConnection::encodeCursor(3), "__data" => $userType]);
        $this->assertNull($type);
    }

    public function testIfUserExceptionIsThrownIfCursorIsInvalid()
    {
        $resolver = $this->getResolver();
        $collection = $this->createMock(Collection::class);
        $userType = $this->createMock(UserType::class);
        $userType->method("getCharacters")->willReturn($collection);
        $characterTypes = [
            $this->createMock(Character::class),
            $this->createMock(Character::class),
            $this->createMock(Character::class),
        ];
        for ($i = 1; $i <= count($characterTypes); $i++) {
            $characterTypes[$i-1]->method("getId")->willReturn($i);
        }
        $collection->method("slice")->will($this->returnCallback(function($offset, $limit) use ($characterTypes) {
            return array_slice($characterTypes, $offset, $limit);
        }));

        $this->expectException(UserError::class);
        $type = $resolver->getCharacterFromCursor(["cursor" => "MakesnoSense", "__data" => $userType]);
    }
}

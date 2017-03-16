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
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\TypeGuardian;

class CharacterResolverTest extends WebTestCase
{
    protected function getResolver()
    {
        $resolver = new CharacterResolver();
        $this->startupService($resolver);

        return $resolver;
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
        $this->assertInstanceOf(TypeGuardian::class, $return);
        $this->assertTrue($return->isTypeOf(CharacterType::class));
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
        $this->assertInstanceOf(TypeGuardian::class, $return);
        $this->assertTrue($return->isTypeOf(CharacterType::class));
        $this->assertSame("One", $return->getName());
    }

    public function testIfCharacterResolverWithIncorrectNameReturnsNull()
    {
        $resolver = $this->getResolver();

        $return = $resolver->resolve($this->getMockedArgument(["characterName" => "None"]));
        $this->assertNull($return);
    }
}

<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests;

use LotGD\Core\Game;
use LotGD\Core\Models\Character;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\CharacterType;

class CharacterTypeTest extends WebTestCase
{
    protected function getCharacterMock(array $methodReturns = []): Character
    {
        $characterMock = $this->getMockBuilder(Character::class)
            ->disableOriginalConstructor()
            ->setMethods(array_keys($methodReturns))
            ->getMock();

        foreach ($methodReturns as $method => $return) {
            $characterMock->method($method)->willReturn($return);
        }

        return $characterMock;
    }

    protected function getGameMock(): Game
    {
        $gameMock = $this->getMockBuilder(Game::class)
            ->disableOriginalConstructor()
            ->setMethods(["getEntityManager"])
            ->getMock();

        $entityManagerMock = $this->createMock(\Doctrine\ORM\EntityManager::class);
        $entityManagerMock->method("getRepository")->willReturn(new class {
            public function find() {
                return new class {
                    public function getTitle() {
                        return "mockTitle";
                    }
                };
            }
        });

        $gameMock->method("getEntityManager")->willReturn($entityManagerMock);

        return $gameMock;
    }

    public function testCharacterTypeConstructor()
    {
        $type = new CharacterType(
            $this->getGameMock(),
            $this->getCharacterMock()
        );
        $this->assertInstanceOf(CharacterType::class, $type);
    }

    public function testIfIdValueIsSameAsInEntity()
    {
        $type = new CharacterType(
            $this->getGameMock(),
            $this->getCharacterMock(["getId" => 123451])
        );
        // CharacterType needs to return a string, not an integer.
        $this->assertSame("123451", $type->getId());
    }

    public function testIfNameIsSameAsInEntity()
    {
        $type = new CharacterType(
            $this->getGameMock(),
            $this->getCharacterMock(["getName" => "Luke"])
        );
        $this->assertSame("Luke", $type->getName());
    }

    public function testIfDisplayNameIsSameAsInEntity()
    {
        $type = new CharacterType(
            $this->getGameMock(),
            $this->getCharacterMock(["getDisplayName" => "Knight Luke"])
        );
        $this->assertSame("Knight Luke", $type->getDisplayName());
    }
}

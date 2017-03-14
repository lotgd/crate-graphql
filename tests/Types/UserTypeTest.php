<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests;

use Doctrine\Common\Collections\Collection;
use LotGD\Core\Game;
use LotGD\Crate\GraphQL\Models\User;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType;

class UserTypeTest extends WebTestCase
{
    protected function getGameMock(): Game
    {
        $gameMock = $this->createMock(Game::class);
        return $gameMock;
    }

    protected function getUserMock($methods = []): User
    {
        $userMock = $this->createMock(User::class);

        foreach ($methods as $method => $willReturn) {
            $userMock->method($method)->willReturn($willReturn);
        }

        return $userMock;
    }

    public function testIfUserTypeReturnsCorrectId()
    {
        $id = 12356;
        $userMock = $this->getUserMock(["getId" => $id]);
        $type = new UserType($this->getGameMock(), $userMock);

        $this->assertSame((string)$id, $type->getId());
    }

    public function testIfUserTypeReturnsCorrectName()
    {
        $name = "admin";
        $userMock = $this->getUserMock(["getName" => $name]);
        $type = new UserType($this->getGameMock(), $userMock);

        $this->assertSame($name, $type->getName());
    }

    public function testIfUserTypeReturnsGenerator()
    {
        $collectionMock = $this->createMock(Collection::class);
        $userMock = $this->getUserMock(["getCharacters" => $collectionMock]);
        $type = new UserType($this->getGameMock(), $userMock);

        $this->assertInstanceOf(\Generator::class, $type->getCharacters());
    }
}

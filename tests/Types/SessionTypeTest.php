<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests;

use LotGD\Core\Game;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\SessionType;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\UserType;

class SessionTypeTest extends WebTestCase
{
    protected function getGameMock(): Game
    {
        $gameMock = $this->createMock(Game::class);
        return $gameMock;
    }

    protected function getUserTypeMock(): UserType
    {
        $userTypeMock = $this->createMock(UserType::class);
        return $userTypeMock;
    }

    public function testSetAndGetUser()
    {
        $type = new SessionType($this->getGameMock());
        $userTypeMock = $this->getUserTypeMock();

        $this->assertNull($type->getUser());
        $type->setUser($userTypeMock);
        $this->assertSame($userTypeMock, $type->getUser());
    }

    public function testSetAndGetApiKey()
    {
        $type = new SessionType($this->getGameMock());
        $apiKey = "asdasdasde2q3sfds";

        $this->assertNull($type->getAuthToken());
        $type->setAuthToken($apiKey);
        $this->assertSame($apiKey, $type->getAuthToken());
    }

    public function testSetAndGetExpirationDate()
    {
        $type = new SessionType($this->getGameMock());
        $expirationDate = "2017-12-01 13:15:16";

        $this->assertNull($type->getExpiresAt());
        $type->setExpiresAt($expirationDate);
        $this->assertSame($expirationDate, $type->getExpiresAt());
    }
}

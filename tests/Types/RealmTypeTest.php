<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests;

use LotGD\Core\Game;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\RealmType;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\ConfigurationType;

class RealmTypeTest extends WebTestCase
{
    protected function getGameMock(): Game
    {
        $gameMock = $this->createMock(Game::class);
        return $gameMock;
    }

    public function testRealmTypeConstructor()
    {
        $type = new RealmType($this->getGameMock());
        $this->assertInstanceOf(RealmType::class, $type);
    }

    public function testIfRealmGetConfigurationReturnsConfigurationType()
    {
        $type = new RealmType($this->getGameMock());
        $this->assertInstanceOf(ConfigurationType::class, $type->getConfiguration());
    }
}

<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests;

use LotGD\Core\Game;
use LotGD\Core\Action;
use LotGD\Core\ActionGroup;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\ActionType;

class ActionTypeTest extends WebTestCase
{
    protected function getActionMock(array $methodReturns = []): Action
    {
        $actionMock = $this->getMockBuilder(Action::class)
            ->disableOriginalConstructor()
            ->setMethods(array_keys($methodReturns))
            ->getMock();

        foreach ($methodReturns as $method => $return) {
            $actionMock->method($method)->willReturn($return);
        }

        return $actionMock;
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

    public function testActionGroupTypeConstructor()
    {
        $type = new ActionType(
            $this->getGameMock(),
            $this->getActionMock()
        );
        $this->assertInstanceOf(ActionType::class, $type);
    }

    public function testIfIdValueIsSameAsInEntity()
    {
        $type = new ActionType(
            $this->getGameMock(),
            $this->getActionMock(["getId" => "id"])
        );
        $this->assertSame("id", $type->getId());
    }

    public function testIfTitleIsSameAsInEntity()
    {
        $type = new ActionType(
            $this->getGameMock(),
            $this->getActionMock()
        );
        $this->assertSame("mockTitle", $type->getTitle());
    }
}

<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests;

use LotGD\Core\Game;
use LotGD\Core\Action;
use LotGD\Core\ActionGroup;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\ActionGroupType;

class ActionGroupTypeTest extends WebTestCase
{
    protected function getActionMock(string $title)
    {
        $actionEntity = $this->getMockBuilder(Action::class)
            ->disableOriginalConstructor()
            ->setMethods(["getId"])
            ->getMock();
        $actionEntity->method("getId")->willReturn($title);
        return $actionEntity;
    }

    protected function getActionGroupEntityMock(array $methodReturns = []): ActionGroup
    {
        $actionGroupEntity = $this->getMockBuilder(ActionGroup::class)
            ->disableOriginalConstructor()
            ->setMethods(array_keys($methodReturns))
            ->getMock();

        foreach ($methodReturns as $method => $return) {
            $actionGroupEntity->method($method)->willReturn($return);
        }

        return $actionGroupEntity;
    }

    protected function getGameMock(): Game
    {
        $gameMock = $this->getMockBuilder(Game::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $gameMock;
    }

    public function testActionGroupTypeConstructor()
    {
        $type = new ActionGroupType(
            $this->getGameMock(),
            $this->getActionGroupEntityMock()
        );
        $this->assertInstanceOf(ActionGroupType::class, $type);
    }

    public function testIfIdValueIsSameAsInEntity()
    {
        $type = new ActionGroupType(
            $this->getGameMock(),
            $this->getActionGroupEntityMock(["getId" => "id"])
        );
        $this->assertSame("id", $type->getId());
    }

    public function testIfTitleIsSameAsInEntity()
    {
        $type = new ActionGroupType(
            $this->getGameMock(),
            $this->getActionGroupEntityMock(["getTitle" => "ThatTitle"])
        );
        $this->assertSame("ThatTitle", $type->getTitle());
    }

    public function testIfTSortKeyIsSameAsInEntity()
    {
        $type = new ActionGroupType(
            $this->getGameMock(),
            $this->getActionGroupEntityMock(["getSortKey" => 12315])
        );
        $this->assertSame(12315, $type->getSortKey());
    }

    public function testifGetActionsCorrectlyReturnsAGeneratorWhichYieldDesiredChilds()
    {
        $list = [
            $this->getActionMock("A"),
            $this->getActionMock("B"),
            $this->getActionMock("C"),
            $this->getActionMock("D"),
        ];

        $type = new ActionGroupType(
            $this->getGameMock(),
            $this->getActionGroupEntityMock(["getActions" => $list])
        );

        $generator = $type->getActions();
        $this->assertInstanceOf(\Generator::class, $generator);
        $i = 0;
        foreach ($generator as $child) {
            $this->assertSame($list[$i]->getId(), $child->getId());
            $i++;
        }

        $this->assertCount($i, $list);
    }
}

<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests;

use Generator;

use LotGD\Core\Game;
use LotGD\Core\Models\Viewpoint;
use LotGD\Core\ActionGroup;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\ActionGroupType;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\ViewpointType;

/**
 * Description of ViewpointTypeTest
 */
class ViewpointTypeTest extends WebTestCase
{
    protected function getGameMock(): Game
    {
        $gameMock = $this->createMock(Game::class);
        return $gameMock;
    }

    protected function getViewpointMock(array $methods = []): Viewpoint
    {
        $viewpointMock = $this->createMock(Viewpoint::class);

        foreach ($methods as $method => $willReturn) {
            $viewpointMock->method($method)->willReturn($willReturn);
        }

        return $viewpointMock;
    }

    protected function getActionGroupEntityMock(): ActionGroup
    {
        $actionGroupEntity = $this->createMock(ActionGroup::class);
        return $actionGroupEntity;
    }

    public function testViewpointTypeReturnsCorrectTitle()
    {
        $willReturn = "The Village Square";
        $type = new ViewpointType(
            $this->getGameMock(),
            $this->getViewpointMock(["getTitle" => $willReturn])
        );

        $this->assertSame($willReturn, $type->getTitle());
    }

    public function testViewpointTypeReturnsCorrectDescription()
    {
        $willReturn = "It's all nice and quite on the village square.\n\nBirds are flying around...";
        $type = new ViewpointType(
            $this->getGameMock(),
            $this->getViewpointMock(["getDescription" => $willReturn])
        );

        $this->assertSame($willReturn, $type->getDescription());
    }

    public function testViewpointTypeReturnsCorrectTemplate()
    {
        $willReturn = "core/village";
        $type = new ViewpointType(
            $this->getGameMock(),
            $this->getViewpointMock(["getTemplate" => $willReturn])
        );

        $this->assertSame($willReturn, $type->getTemplate());
    }

    public function testViewpointTypeReturnsListOfActionGroupTypes()
    {
        $willReturn = [
            $this->getActionGroupEntityMock(),
            $this->getActionGroupEntityMock(),
            $this->getActionGroupEntityMock()
        ];
        $type = new ViewpointType(
            $this->getGameMock(),
            $this->getViewpointMock(["getActionGroups" => $willReturn])
        );


        $return = $type->getActionGroups();
        $this->assertInstanceOf(Generator::class, $return);
        $i = 0;
        foreach ($return as $actionGroup) {
            $this->assertInstanceOf(ActionGroupType::class, $actionGroup);
            $i++;
        }
        $this->assertCount($i, $willReturn);
    }
}

<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests;

use LotGD\Core\Game;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\ConfigurationType;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\LibraryType;

class ConfigurationTypeTest extends WebTestCase
{
    protected function getGameMock(): Game
    {
        $gameMock = $this->getMockBuilder(Game::class)
            ->disableOriginalConstructor()
            ->setMethods(["getComposerManager", "getModuleManager"])
            ->getMock();

        $composerManagerMock = $this->createMock(\LotGD\Core\ComposerManager::class);
        $moduleManagerMock = $this->createMock(\LotGD\Core\ModuleManager::class);
        $composerMock = $this->createMock(\Composer\Composer::class);

        $packageMock1 = $this->createMock(\Composer\Package\CompletePackage::class);
        $moduleMock1 = $this->createMock(\LotGD\Core\Models\Module::class);
        $moduleMock1->method("getLibrary")->willReturn($packageMock1);

        $packageMock2 = $this->createMock(\Composer\Package\CompletePackage::class);
        $moduleMock2 = $this->createMock(\LotGD\Core\Models\Module::class);
        $moduleMock2->method("getLibrary")->willReturn($packageMock2);

        $gameMock->method("getComposerManager")->willReturn($composerManagerMock);
        $gameMock->method("getModuleManager")->willReturn($moduleManagerMock);

        $composerManagerMock->method("getComposer")->willReturn($composerMock);
        $moduleManagerMock->method("getModules")->willReturn([$moduleMock1, $moduleMock2]);

        return $gameMock;
    }

    public function testConfigurationTypeConstructor()
    {
        $type = new ConfigurationType($this->getGameMock());
        $this->assertInstanceOf(ConfigurationType::class, $type);
    }

    public function testIfGetCoreReturnsLibraryType()
    {
        $type = new ConfigurationType($this->getGameMock());
        $this->assertInstanceOf(LibraryType::class, $type->getCore());
    }

    public function testIfGetCrateReturnsLibraryType()
    {
        $type = new ConfigurationType($this->getGameMock());
        $this->assertInstanceOf(LibraryType::class, $type->getCrate());
    }

    public function testIfGetModulesReturnsValidModuleList()
    {
        $type = new ConfigurationType($this->getGameMock());
        $modules = $type->getModules();

        $this->assertInstanceOf(\Generator::class, $modules);

        $i = 0;
        foreach($modules as $module) {
            $this->assertInstanceOf(LibraryType::class, $module);
            $i++;
        }

        $this->assertSame(2, $i);
    }
}

<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests;

use LotGD\Core\Game;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\LibraryType;

class LibraryTypeTest extends WebTestCase
{
    protected function getGameMock($packageMethods = []): Game
    {
        $gameMock = $this->getMockBuilder(Game::class)
            ->disableOriginalConstructor()
            ->setMethods(["getComposerManager"])
            ->getMock();

        $composerManagerMock = $this->createMock(\LotGD\Core\ComposerManager::class);
        $composerMock = $this->createMock(\Composer\Composer::class);

        $packageMock = $this->createMock(\Composer\Package\CompletePackage::class);
        foreach ($packageMethods as $method => $return) {
            $packageMock->method($method)->willReturn($return);
        }

        $gameMock->method("getComposerManager")->willReturn($composerManagerMock);
        $composerManagerMock->method("getComposer")->willReturn($composerMock);
        $composerManagerMock->method("getPackageForLibrary")->willReturn($packageMock);
        $composerMock->method("getPackage")->willReturn($packageMock);

        return $gameMock;
    }

    public function testLibraryTypeConstructorWithoutLibrary()
    {
        $type = new LibraryType($this->getGameMock());
        $this->assertInstanceOf(LibraryType::class, $type);

        $type = new LibraryType($this->getGameMock(), "lotgd/core");
        $this->assertInstanceOf(LibraryType::class, $type);
    }

    public function testLibraryTypeGetNameReturnsPrettyNameOfPackage()
    {
        $return = "This is the pretty name of the library.";
        $type = new LibraryType($this->getGameMock(["getPrettyName" => $return]));
        $this->assertSame($return, $type->getName());
    }

    public function testLibraryTypeGetNameReturnsEmptyNameIfPrettyNameOfPackageIsNull()
    {
        $type = new LibraryType($this->getGameMock(["getPrettyName" => null]));
        $this->assertSame("", $type->getName());
    }

    public function testLibraryTypeGetVersionReturnsPrettyVersionOfPackage()
    {
        $return = "This is the pretty version of the library.";
        $type = new LibraryType($this->getGameMock(["getPrettyVersion" => $return]));
        $this->assertSame($return, $type->getVersion());
    }

    public function testLibraryTypeGetVersionReturnsEmptyVersionIfPrettyVersionOfPackageIsNull()
    {
        $type = new LibraryType($this->getGameMock(["getPrettyVersion" => null]));
        $this->assertSame("", $type->getVersion());
    }

    public function testLibraryTypeGetLibraryReturnsNameOfPackage()
    {
        $return = "This is the actual name of the package, or library in lotgd/core.";
        $type = new LibraryType($this->getGameMock(["getName" => $return]));
        $this->assertSame($return, $type->getLibrary());
    }

    public function testLibraryTypeGetLibraryReturnsEmptyLibraryIfNameOfPackageIsNull()
    {
        $type = new LibraryType($this->getGameMock(["getName" => null]));
        $this->assertSame("", $type->getLibrary());
    }

    public function testLibraryTypeGetUrlReturnsSourceUrlOfPackage()
    {
        $return = "Source-URL";
        $type = new LibraryType($this->getGameMock(["getSourceUrl" => $return]));
        $this->assertSame($return, $type->getUrl());
    }

    public function testLibraryTypeGetUrlReturnsEmptyUrlIfSourceUrlOfPackageIsNull()
    {
        $type = new LibraryType($this->getGameMock(["getSourceUrl" => null]));
        $this->assertSame("", $type->getUrl());
    }

    public function testLibraryTypeGetAuthorReturnsFormattedListOfPackageAuthors()
    {
        $type = new LibraryType($this->getGameMock(["getAuthors" => [
            ["name" => "Arthur Cohn", "email" => "arthur.cohn@example.com"],
            ["name" => "Joseph E. Levine", "email" => "joseph.levine@example.com"],
        ]]));
        $this->assertSame(
            "Arthur Cohn (arthur.cohn@example.com), "
                ."Joseph E. Levine (joseph.levine@example.com)",
            $type->getAuthor()
        );
    }

    public function testLibraryTypeGetAuthorReturnsFormattedListOfPackageAuthorsIfEmailIsMissing()
    {
        $type = new LibraryType($this->getGameMock(["getAuthors" => [
            ["name" => "Arthur Cohn"],
            ["name" => "Joseph E. Levine"],
        ]]));
        $this->assertSame(
            "Arthur Cohn, "
                ."Joseph E. Levine",
            $type->getAuthor()
        );
    }

    public function testLibraryTypeGetAuthorReturnsFormattedListOfPackageAuthorsIfNameIsMissing()
    {
        $type = new LibraryType($this->getGameMock(["getAuthors" => [
            ["email" => "arthur.cohn@example.com"],
            ["email" => "joseph.levine@example.com"],
        ]]));
        $this->assertSame(
            "arthur.cohn@example.com, "
                ."joseph.levine@example.com",
            $type->getAuthor()
        );
    }

    public function testLibraryTypeGetAuthorReturnsFormattedListOfPackageAuthorsIfMixedIsMissing()
    {
        $type = new LibraryType($this->getGameMock(["getAuthors" => [
            ["name" => "Arthur Cohn", "email" => "arthur.cohn@example.com"],
            ["name" => "Joseph E. Levine"],
            ["email" => "vittorio.desica@example.com"]
        ]]));
        $this->assertSame(
            "Arthur Cohn (arthur.cohn@example.com), "
                ."Joseph E. Levine, "
                ."vittorio.desica@example.com",
            $type->getAuthor()
        );
    }

    public function testLibraryTypeGetAuthorReturnsFormattedListOfPackageAuthorsIfOnElementIsEmpty()
    {
        $type = new LibraryType($this->getGameMock(["getAuthors" => [
            ["name" => "Arthur Cohn", "email" => "arthur.cohn@example.com"],
            ["_wrong" => "Joseph E. Levine"],
            ["email" => "vittorio.desica@example.com"]
        ]]));
        $this->assertSame(
            "Arthur Cohn (arthur.cohn@example.com), "
                ."vittorio.desica@example.com",
            $type->getAuthor()
        );
    }

    public function testLibraryTypeGetAuthorsReturnsUnknownAuthorIfGetAuthorsIsNull()
    {
        $type = new LibraryType($this->getGameMock(["getAuthors" => null]));
        $this->assertSame("unknown", $type->getAuthor());
    }
}

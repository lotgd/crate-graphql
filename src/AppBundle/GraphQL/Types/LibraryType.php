<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;

/**
 */
class LibraryType
{
    private $game;
    
    public $name;
    public $version;
    public $library;
    public $url;
    public $author;
    
    public function __construct(Game $game, string $library = null) {
        $this->game = $game;
        
        if (empty($library)) {
            $composerPackage = $game->getComposerManager()->getComposer()->getPackage();
        } else {
            $composerPackage = $game->getComposerManager()->getPackageForLibrary($library);
            #\Doctrine\Common\Util\Debug::dump($game->getComposerManager()->getPackages());
        }
        
        $this->name = function() use ($composerPackage) { return $composerPackage->getPrettyName(); };
        $this->version = function() use ($composerPackage) { return $composerPackage->getPrettyVersion(); };
        $this->library = function() use ($composerPackage) { return $composerPackage->getName(); };
        $this->url = function() use ($composerPackage) { return $composerPackage-> getSourceUrl(); };
        $this->author = function() use ($composerPackage) { return $this->formatAuthors($composerPackage->getAuthors()); };
    }
    
    protected function formatAuthors(array $authors = null) {
        if ($authors === null) {
            return $authors;
        }
    }
}

<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;

/**
 * Represents the Library type.
 */
class LibraryType
{
    /** @var Game The game instance. */
    private $game;
    
    /** @var closure Returns the name of the library */
    public $name;
    /** @var closure Returns the version of the library */
    public $version;
    /** @var closure Returns the package name of the library */
    public $library;
    /** @var closure Returns the url of the library */
    public $url;
    /** @var closure Returns the authors of the library */
    public $author;
    
    /**
     * @param Game $game
     * @param string $library
     */
    public function __construct(Game $game, string $library = null) {
        $this->game = $game;
        
        if (empty($library)) {
            $composerPackage = $game->getComposerManager()->getComposer()->getPackage();
        } else {
            $composerPackage = $game->getComposerManager()->getPackageForLibrary($library);
        }
        
        $this->name = function() use ($composerPackage) { return $composerPackage->getPrettyName(); };
        $this->version = function() use ($composerPackage) { return $composerPackage->getPrettyVersion(); };
        $this->library = function() use ($composerPackage) { return $composerPackage->getName(); };
        $this->url = function() use ($composerPackage) { return $composerPackage-> getSourceUrl(); };
        $this->author = function() use ($composerPackage) { return $this->formatAuthors($composerPackage->getAuthors()); };
    }
    
    /**
     * Returns a string of a list of authors.
     * @param array $authors
     * @return array
     */
    protected function formatAuthors(array $authors = null): string
    {
        if ($authors === null) {
            return "unknown";
        }
        
        $list = "";
        foreach ($authors as $author) {
            $list .= "${author['name']} (${author['email']}), ";
        }
        
        return substr($list, 0, -2);
    }
}

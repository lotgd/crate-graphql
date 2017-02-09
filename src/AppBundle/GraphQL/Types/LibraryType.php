<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use LotGD\Core\Game;

/**
 * Represents the GraphQL Library type.
 */
class LibraryType extends BaseType
{
    /** @var Composer\Package\CompletePackageInterface */
    private $composerPackage;

    /**
     * @param Game $game
     * @param string $library
     */
    public function __construct(Game $game, string $library = null) {
        parent::__construct($game);

        if (empty($library)) {
            $this->composerPackage = $game->getComposerManager()->getComposer()->getPackage();
        } else {
            $this->composerPackage = $game->getComposerManager()->getPackageForLibrary($library);
        }
    }

    /**
     * Returns the human readable name of this library
     * @return string
     */
    public function getName(): string
    {
        return $this->composerPackage->getPrettyName()?:"";
    }

    /**
     * Returns the version number.
     * @return string
     */
    public function getVersion(): string
    {
        return $this->composerPackage->getPrettyVersion()?:"";
    }

    /**
     * Returns the short package name (like lotgd/core).
     * @return string
     */
    public function getLibrary(): string
    {
        return $this->composerPackage->getName()?:"";
    }

    /**
     * Returns a url to read more about this package.
     * @return string
     */
    public function getUrl(): string
    {
        return $this->composerPackage->getSourceUrl()?:"";
    }

    /**
     * Returns the list of authors as a comma separated list, including e-mails.
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->formatAuthors($this->composerPackage->getAuthors());
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
            if (isset($author['email']) && isset($author["name"])) {
                $list .= "${author['name']} (${author['email']}), ";
            } elseif (isset($author["name"])) {
                $list .= "${author['name']}, ";
            } elseif (isset($author["email"])) {
                $list .= "${author['email']}, ";
            } else {
                continue;
            }
        }

        return substr($list, 0, -2);
    }
}

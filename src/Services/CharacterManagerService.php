<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Services;

use Exception;
use Doctrine\DBAL\DBALException;

use LotGD\Core\Models\Character;
use LotGD\Crate\GraphQL\Exceptions\CharacterNameExistsException;
use LotGD\Crate\GraphQL\Exceptions\CrateException;

/**
 * Management class for everything user account related.
 */
class CharacterManagerService extends BaseManagerService
{
    /**
     * Creates a new character
     * @param string $name
     * @return Character
     * @throws CharacterNameExistsException If character is already taken
     * @throws CrateException If some unknown character occured during saving.
     */
    public function createNewCharacter(string $name): Character
    {
        $character = $this->findByName($name);

        if ($character) {
            throw new CharacterNameExistsException("Character with name {$name} already taken.");
        }

        try {
            $character = Character::createAtFullHealth(["name" => $name]);
            $character->save($this->getEntityManager());
        } catch (DBALException $ex) {
            throw new CrateException("An unknown DBALException occured: " . $ex->getMessage());
        } catch (Exception $ex) {
            throw new CrateException("An unknown Exception occured: " . $ex->getMessage());
        }

        return $character;
    }

    /**
     * Finds a character by id.
     * @param int $id
     * @return Character|null
     */
    public function findById(int $id): ?Character
    {
        return $this->getOneById(Character::class, $id);
    }

    /**
     * Finds a character by id.
     * @param string $name
     * @return Character|null
     */
    public function findByName(string $name): ?Character
    {
        return $this->getOneBy(Character::class, ["name" => $name]);
    }
}

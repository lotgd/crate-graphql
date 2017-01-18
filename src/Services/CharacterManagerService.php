<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Services;

use LotGD\Core\Models\Character;

/**
 * Management class for everything user account related.
 */
class CharacterManagerService extends BaseManagerService
{
    public function createNewCharacter(string $name): Character
    {
        $character = $this->findByName($name);

        if ($character) {
            throw new Exception("User with name {$name} already taken.");
        }

        return Character::createAtFullHealth(["name" => $name]);
    }

    /**
     * Finds a character by id.
     * @param int $id
     * @return type
     */
    public function findById(int $id)
    {
        return $this->getOneById(Character::class, $id);
    }

    /**
     * Finds a character by id.
     * @param int $name
     * @return type
     */
    public function findByName(string $name)
    {
        return $this->getOneBy(Character::class, ["name" => $name]);
    }
}

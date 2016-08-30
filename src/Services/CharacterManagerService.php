<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Services;

use LotGD\Core\Models\Character;

/**
 * Management class for everything user account related.
 */
class CharacterManagerService extends BaseManagerService
{
    /**
     * Finds a character by id.
     * @param int $id
     * @return type
     */
    public function findById(int $id)
    {
        return $this->getOneById(Character::class, $id);
    }
}

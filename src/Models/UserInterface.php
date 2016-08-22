<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Models;

use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

/**
 * UserInterface which extends symfonys interface and provides additional method needed by our own auth mechanisms.
 */
interface UserInterface extends SymfonyUserInterface
{
    /**
     * Returns the user id
     * @return int
     */
    public function getId(): int;
    /**
     * Changes a user's password
     * @param string $password New plain text password, gets hashed
     */
    public function setPassword(string $password);
    /**
     * Verifies if a plain text password hashes to the same value as the hash stored for this user.
     * @param string $password
     * @return bool True if password is the same, False if not.
     */
    public function verifyPassword(string $password): bool;
}

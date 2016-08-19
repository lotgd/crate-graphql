<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LotGD\Crate\GraphQL\Models;

use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

/**
 *
 * @author sauterb
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

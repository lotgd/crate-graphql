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
    public function setPassword(string $password);
    public function verifyPassword(string $password): bool;
}

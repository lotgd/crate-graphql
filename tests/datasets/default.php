<?php

use LotGD\Crate\GraphQL\Models\User;

$user = new User("admin", "12345");

$em->persist($user);
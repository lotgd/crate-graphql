<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tools;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\ORM\EntityManagerInterface;

interface EntityManagerAwareInterface extends ContainerAwareInterface
{
    public function getEntityManager(): EntityManagerInterface;
}
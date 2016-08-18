<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tools;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Doctrine\ORM\EntityManagerInterface;

trait EntityManagerAwareTrait 
{
    use ContainerAwareTrait;
    
    public function getEntityManager(): EntityManagerInterface {
        return $this->container->get('lotgd.core.game')->getEntityManager();
    }
}
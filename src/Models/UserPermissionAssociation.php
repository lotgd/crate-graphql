<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Models;

use LotGD\Core\Models\PermissionAssociationInterface;
use LotGD\Core\Tools\Model\PermissionAssociationable;


/**
 * Associates users with permissions
 * @Entity
 * @Table("user_permission_associations")
 */
class UserPermissionAssociation implements PermissionAssociationInterface {
    use PermissionAssociationable;

    /**
     * @Id @ManyToOne(targetEntity="User", inversedBy="permissions")
     * @JoinColumn(name="owner", referencedColumnName="id")
     */
    private $owner;
}
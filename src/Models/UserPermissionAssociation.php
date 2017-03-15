<?php
/**
 * Created by PhpStorm.
 * User: sauterb
 * Date: 15/03/17
 * Time: 09:38
 */

namespace LotGD\Crate\GraphQL\Models;

use LotGD\Core\Models\PermissionAssociationInterface;
use LotGD\Core\Tools\Model\PermissionAssociationable;


/**
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
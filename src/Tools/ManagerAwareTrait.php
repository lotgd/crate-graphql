<?php
/**
 * Created by PhpStorm.
 * User: sauterb
 * Date: 15/03/17
 * Time: 19:41
 */

namespace LotGD\Crate\GraphQL\Tools;

use LotGD\Crate\GraphQL\Services\AuthorizationService;
use LotGD\Crate\GraphQL\Services\CharacterManagerService;
use LotGD\Crate\GraphQL\Services\UserManagerService;

/**
 * This trait gives easier access to certain game managing services.
 *
 * The Service using this trait must be container aware!
 * @package LotGD\Crate\GraphQL\Tools
 */
trait ManagerAwareTrait
{
    /**
     * Returns the service for managing user accounts
     * @return UserManagerService
     */
    public function getUserManager(): UserManagerService
    {
        return $this->container->get("lotgd.crate.graphql.user_manager");
    }

    /**
     * Returns the service for managing characters
     * @return CharacterManagerService
     */
    public function getCharacterManager(): CharacterManagerService
    {
        return $this->container->get("lotgd.crate.graphql.character_manager");
    }

    /**
     * Returns the service to check and control access rights.
     * @return AuthorizationService
     */
    public function getAuthorizationService(): AuthorizationService
    {
        return $this->container->get("lotgd.authorization");
    }
}
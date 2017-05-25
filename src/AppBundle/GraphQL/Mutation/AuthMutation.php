<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Mutation;

use Doctrine\DBAL\DBALException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Error\UserError;

use LotGD\Crate\GraphQL\{
    Exceptions\UserEmailExistsException,
    Exceptions\UserNameExistsException,
    Models\User,
    Models\ApiKey,
    Tools\EntityManagerAwareInterface,
    Tools\EntityManagerAwareTrait
};


/**
 * Resolver for authentication mutations
 */
class AuthMutation implements EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;

    /**
     * Authenticates an user with a password
     * @param string $email
     * @param string $password
     * @return array GraphQL answer with the fields apiKey and expiresAt
     * @throws UserError If the login Credentials are invalid.
     */
    function authWithPassword(string $email = null, string $password = null)
    {
        $userManager = $this->container->get("lotgd.crate.graphql.user_manager");

        $user = $userManager->findByEmail($email);

        if ($user instanceof User) {
            $passwordVerified = $user->verifyPassword($password);
        }

        // Do not tell if user is unknown or password wrong
        if ($user === null || $passwordVerified === false) {
            // Throw a UserError - this gets catched by the GraphQL bundle to deliver a
            // valid graphql error.
            throw new UserError("The login credentials are invalid.");
        }

        // Generate api key
        if ($user->hasApiKey() === false) {
            $key = ApiKey::generate($user);
            $user->setApiKey($key);
        } elseif ($user->getApiKey()->isValid() === false) {
            // Delete old key
            $oldKey = $user->getApiKey();
            $oldKey->delete($this->getEntityManager());
            unset($oldKey);

            // Create new key
            $newKey = ApiKey::generate($user);
            $user->setApiKey($newKey);

            $key = $newKey;
        }
        else {
            $key = $user->getApiKey();
        }

        // Refresh last used
        $key->setLastUsed();
        // Save the key and flush.
        $key->save($this->getEntityManager());

        $argument = new Argument(["apiKey" => $key->getApiKey()]);
        $return = $this->container->get("app.graph.resolver.session")->resolve($argument);

        return [
            "session" => $return,
        ];
    }

    function createPasswordUser(string $name = "", string $email = "", string $password = "")
    {
        $userManager = $this->container->get("lotgd.crate.graphql.user_manager");

        try {
            $userManager->createNewWithPassword($name, $email, $password);
        } catch (UserNameExistsException $ex) {
            throw new UserError("Username is already in use.");
        } catch (UserEmailExistsException $ex) {
            throw new UserError("Email address is already in use.");
        } catch (\Exception $ex) {
            throw new UserError("An unknown exception occured: " . $ex->getMessage());
        }

        // @ToDo add check to only return session if server settings allow this without email validation.
        return $this->authWithPassword($email, $password);
    }
}

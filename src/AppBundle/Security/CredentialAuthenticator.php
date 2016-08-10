<?php

namespace LotGD\Crate\GraphQL\AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;

use LotGD\Crate\GraphQL\Models\User;
use LotGD\Crate\GraphQL\Models\UserInterface;

/**
 * CredentialAuthenticator
 */
class CredentialAuthenticator extends AbstractGuardAuthenticator implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    const TYPE_PASSWORD = 1;
    
    /**
     * Constructs credentials from the request which get passed to $this->selfUser().
     * @param Request $request
     * @return array|null Null if credentials are missing, else array containing credentials
     */
    public function getCredentials(Request $request)
    {
        // We do not support authentification via get
        if ($request->isMethod("POST") === false) {
            throw new AuthentificationException("Auth via anything else than POST is not supported.");
            return;
        }
        
        if (strlen($request->getContent()) > 0) {
            $content = json_decode($request->getContent(), true, 2);
            
            // no valid json
            if ($content === null) {
                throw new AuthentificationException("No valid json data has been pushed");
            }
            
            // password auth
            if (isset($content["password"]) && isset($content["email"])) {
                $credentials = [
                    "type" => self::TYPE_PASSWORD,
                    "password" => $content["password"],
                    "email" => $content["email"]
                ];
            } elseif (isset($content["oauth2"])) {
                $credentials = [
                    "type" => self::OAUTH2,
                    "provider" => $content["oauth2"],
                    "token" => $content["token"] ?? ""
                ];
            }
            else {
                throw new AuthenticationException("The login credentials are not valid.");
            }
        }

        return $credentials ?? [];
    }

    /**
     * Takes the credentials and tries to find the corresponding user.
     * @param type $credentials
     * @param UserProviderInterface $userProvider
     * @return type
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $em = $this->container->get('lotgd.core.game')->getEntityManager();
        
        return $em->getRepository(User::class)
            ->findOneBy(["email" => $credentials["email"]]);
        // if null, authentication will fail
        // if a User object, checkCredentials() is called
        //return $this->em->getRepository(':User')
        //    ->findOneBy(array('apiKey' => $apiKey));
    }

    public function checkCredentials($credentials, SymfonyUserInterface $user)
    {
        if (!$user instanceof UserInterface) {
            $interface = UserInterface::class;
            throw new Exception("\$user given to CredentialAuthenticator->checkCredentials must be implementing {$inteface}");
        }
        
        switch($credentials["type"]) {
            case self::TYPE_PASSWORD:
                if ($user->verifyPassword($credentials["password"])) {
                    return true;
                }
                break;
        }

        return false;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        );

        return new JsonResponse($data, 403);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            // you might translate this message
            'message' => 'Authentication Required'
        );

        return new JsonResponse($data, 401);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}

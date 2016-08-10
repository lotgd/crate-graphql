<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\Security;

use Symfony\Component\HttpFoundation\{
    JsonResponse,
    Request
};
use Symfony\Component\Security\Core\Authentication\Token\ {
    PreAuthenticatedToken,
    TokenInterface
};
use Symfony\Component\Security\Core\ {
    Exception\AuthenticationException,
    User\UserInterface as SymfonyUserInterface,
    User\UserProviderInterface
};
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

use LotGD\Crate\GraphQL\Models\UserInterface;

/**
 * CredentialAuthenticator
 */
class CredentialAuthenticator extends AbstractGuardAuthenticator 
{    
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
            throw new AuthenticationException("Authentication via anything else than POST is not supported.");
        }
        
        if (strlen($request->getContent()) > 0) {
            $content = json_decode($request->getContent(), true, 2);
            
            // no valid json
            if ($content === null) {
                throw new AuthenticationException("No valid json data has been pushed");
            }
            
            // password auth
            if (isset($content["password"]) && isset($content["email"])) {
                return [
                    "type" => self::TYPE_PASSWORD,
                    "password" => $content["password"],
                    "email" => $content["email"]
                ];
            } elseif (isset($content["oauth2"])) {
                return [
                    "type" => self::OAUTH2,
                    "provider" => $content["oauth2"],
                    "token" => $content["token"] ?? ""
                ];
            }
        }

        // If script has not yet returned credentials, let's throw an exception
        throw new AuthenticationException("The login credentials are not valid.");
    }

    /**
     * Takes the credentials and tries to find the corresponding user.
     * @param type $credentials
     * @param UserProviderInterface $userProvider
     * @return type
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByEmail($credentials["email"]);
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
            'message' => $exception->getMessage()
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

<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\ {
    PreAuthenticatedToken,
    TokenInterface
};
use Symfony\Component\Security\Core\ {
    Exception\AuthenticationException,
    User\UserInterface as SymfonyUserInterface,
    User\UserProviderInterface
};
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;


/**
 * TokenAuthenticator
 */
class TokenAuthenticator implements SimplePreAuthenticatorInterface
{
    public function createToken(Request $request, $providerKey)
    {
        // look for a token in
        $apiKey = $request->headers->get('token');

        // no api key means still access to the page.
        if (!$apiKey) {
            return null;
        }

        return new PreAuthenticatedToken(
            'anon.',
            $apiKey,
            $providerKey
        );
    }
    
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof ApiKeyProvider) {
            $userProvider = get_class($userProvider);
            throw new \InvalidArgumentException(
                "The user provider must be an instance of ApiKeyUserProvider ({$userProvider} was given)."
            );
        }

        $apiKey = $token->getCredentials();
        $username = $userProvider->getUsernameForApiKey($apiKey);

        if (!$username) {
            // CAUTION: this message will be returned to the client
            // (so don't put any un-trusted messages / error strings here)
            throw new CustomUserMessageAuthenticationException(
                sprintf('API Key "%s" does not exist.', $apiKey)
            );
        }

        $user = $userProvider->loadUserByUsername($username);

        return new PreAuthenticatedToken(
            $user,
            $apiKey,
            $providerKey,
            $user->getRoles()
        );
    }
}

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
    Exception\CustomUserMessageAuthenticationException,
    User\UserInterface as SymfonyUserInterface,
    User\UserProviderInterface
};
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Overblog\GraphQLBundle\Error\UserError;

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

    
    public function authenticateToken(TokenInterface $token, UserProviderInterface $apiKeyProvider, $providerKey)
    {
        if (!$apiKeyProvider instanceof ApiKeyProvider) {
            $apiKeyProvider = get_class($apiKeyProvider);
            throw new \InvalidArgumentException(
                "The user provider must be an instance of ApiKeyUserProvider ({$apiKeyProvider} was given)."
            );
        }

        $apiKey = $token->getCredentials();
        $user = $apiKeyProvider->getUserForApiKey($apiKey);

        if ($user === null) {
            // User not found, throw exception
            throw new \Exception(
                sprintf('API Key "%s" does not exist.', $apiKey)
            );
        }

        return new PreAuthenticatedToken(
            $user,
            $apiKey,
            $providerKey,
            $user->getRoles()
        );
    }
}

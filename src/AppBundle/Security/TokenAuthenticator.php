<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\ {
    PreAuthenticatedToken,
    TokenInterface
};
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

use LotGD\Crate\GraphQL\Exceptions\AuthenticationException;

/**
 * A pre authenticator used to identify a user via an api key (instead of email/password)
 */
class TokenAuthenticator implements SimplePreAuthenticatorInterface
{
    /**
     * Creates an anonymous pre authenticated symfony token from the authentication data.
     * @param Request $request The current request roken
     * @param type $providerKey Provider key.
     * @return PreAuthenticatedToken
     */
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
    
    /**
     * Returns true if this authenticator supports the given token.
     * @param TokenInterface $token
     * @param type $providerKey
     * @return type
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    
    /**
     * Checks the credentials stored in the token and tries to authenticate it's information.
     * @param TokenInterface $token
     * @param UserProviderInterface $apiKeyProvider
     * @param type $providerKey
     * @return PreAuthenticatedToken
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
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
            throw new AuthenticationException(
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

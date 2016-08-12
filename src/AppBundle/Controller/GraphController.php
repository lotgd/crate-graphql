<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\Controller;

use DateTime;

use Overblog\GraphQLBundle\Controller\GraphController as OverblogGraphController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\ {
    JsonResponse,
    Request
};

use LotGD\Crate\GraphQL\Models\ApiKey;
use LotGD\Crate\GraphQL\Models\UserInterface;

/**
 * Shows either the entry site or performs a query
 */
class GraphController extends OverblogGraphController
{
    /**
     * Main controller action.
     * 
     * This method either displays a landing page informing the user that this is
     * a graphql interface or submits the query to the real controller that manages
     * graphql queries.
     * @Route("/")
     * @param Request $request
     * @return type
     */
    public function endpointAction(Request $request)
    {
        if ($request->isMethod("GET")) {
            $check = "query";
        }
        else {
            $check = "request";
        }
        
        if ($request->$check->has("query")) {
            // Work-around for empty incorrect graphql variables
            if ($request->$check->has("variables") && $request->$check->get("variables") === "") {
                $request->$check->set("variables", "{}");
            }
            
            return parent::endpointAction($request);
        }
        elseif (strlen($request->getContent()) > 0) {
            return parent::endpointAction($request);
        }
        else {
            return $this->render('default/index.html.twig', [
                'base_url' => $request->getUri(),
            ]);
        }
    }
    
    /**
     * @Route("/auth")
     * @param Request $request
     */
    public function authAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        
        $user = $this->getUser();
        $entityManager = $this->get("lotgd.core.game")->getEntityManager();
        
        if ($user->hasApiKey() === false) {
            $key = ApiKey::generate($user);
            $user->setApiKey($key);
        } elseif ($user->getApiKey()->isValid() === false) {
            // Delete old key
            $key = $user->getApiKey();
            $key->delete($entityManager);
            
            // Create new key
            $key = ApiKey::generate($user);
            $user->setApiKey($key);
        }
        else {
            $key = $user->getApiKey();
        }
        
        $key->setLastUsed();
        $key->save($entityManager);
        
        return new JsonResponse([
            "apiKey" => $key->getApiKey(),
            "expiresAt" => $key->getExpiresAtAsString(),
        ]);
    }
}

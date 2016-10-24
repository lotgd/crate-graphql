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
 * Controller for the GraphQL endpoint and the default landing page.
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
    public function endpointAction(Request $request, $schemaName = null)
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
            
            return parent::endpointAction($request, $schemaName);
        }
        elseif (strlen($request->getContent()) > 0) {
            return parent::endpointAction($request, $schemaName);
        }
        else {
            return $this->render('default/index.html.twig', [
                'base_url' => $request->getUri(),
            ]);
        }
    }
}

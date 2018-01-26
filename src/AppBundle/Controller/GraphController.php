<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\Controller;

use DateTime;

use Overblog\GraphQLBundle\Controller\GraphController as OverblogGraphController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\{
    JsonResponse, Request, Response
};

use LotGD\Crate\GraphQL\Models\ApiKey;
use LotGD\Crate\GraphQL\Models\UserInterface;

/**
 * Controller for the GraphQL endpoint and the default landing page.
 */
class GraphController extends OverblogGraphController implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Main controller action.
     * 
     * This method either displays a landing page informing the user that this is
     * a graphql interface or submits the query to the real controller that manages
     * graphql queries.
     * @Route("/")
     * @param Request $request
     * @return Response
     */
    public function endpointAction(Request $request, $schemaName = null): Response
    {
        if ($request->getMethod() == "OPTIONS") {
            $response = new JsonResponse();
        } else {
            $response = parent::endpointAction($request, $schemaName);
        }

        $response->headers->set("Access-Control-Allow-Method", "GET, POST, OPTIONS");
        $response->headers->set("Access-Control-Allow-Headers", "Content-Type, X-Lotgd-Auth-Token, x_lotgd_auth_token");
        $response->headers->set("Access-Control-Allow-Origin", "*");

        return $response;
    }
}

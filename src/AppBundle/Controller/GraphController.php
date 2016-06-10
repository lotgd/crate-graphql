<?php
declare(strict_types=1);

namespace LotGD\Crate\WWW\AppBundle\Controller;

use Overblog\GraphQLBundle\Controller\GraphController as OverblogGraphController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Shows either the entry site or performs a query
 */
class GraphController extends OverblogGraphController
{
    /**
     * Main controller action.$
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
        if ($request->query->has("query")) {
            // Work-around for empty incorrect graphql variables
            if ($request->query->has("variables") && $request->query->get("variables") === "") {
                $request->query->set("variables", "{}");
            }
            
            return parent::endpointAction($request);
        }
        else {
            return $this->render('default/index.html.twig', [
                'base_url' => $request->getUri(),
            ]);
        }
    }
}

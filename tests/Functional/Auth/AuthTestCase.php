<?php

namespace LotGD\Crate\GraphQL\Tests\Functional\Auth;

use LotGD\Crate\GraphQL\Tests\WebTestCase;

/**
 * AuthTestCase
 * @author sauterb
 */
class AuthTestCase extends WebTestCase
{
    protected function sendAuthRequest(array $requestData)
    {
        return $this->sendRequest($this->getUrl("lotgd_crate_graphql_app_graph_auth"), $requestData);
    }
}

<?php

namespace LotGD\Crate\GraphQL\Tests\Functional;

use LotGD\Crate\GraphQL\Tests\WebTestCase;

class GraphiQLPageTest extends WebTestCase
{
    public function testGraphiQLInterfaceGivesStatusOK()
    {
        $client = $this->sendRequest("/graphiql");

        $this->assertStatusCode(200, $client);
    }
}

<?php

namespace LotGD\Crate\GraphQL\Tests\Functional;

use LotGD\Crate\GraphQL\Tests\WebTestCase;

class LandingPageTest extends WebTestCase
{
    public function testLandingPageGivesStatus200()
    {
        $client = $this->sendRequest("/");

        $this->assertStatusCode(200, $client);
    }
}

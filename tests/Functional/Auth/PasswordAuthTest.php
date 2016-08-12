<?php

namespace LotGD\Crate\GraphQL\Tests\Functional\Auth;

/**
 * PasswordAuthTest
 * @author sauterb
 */
class PasswordAuthTest extends AuthTestCase
{
    public function testIfGETRequestFailsWithForbidden()
    {
        $client = $this->sendAuthRequest([
            "method" => "GET"
        ]);
        
        $this->assertStatusCode(403, $client);
        $this->assertJsonResponse([
            "message" => "Authentication via anything else than POST is not supported."
        ], $client);
    }
    
    public function testIfEmptyPostRequestFails()
    {
        $client = $this->sendAuthRequest([
            "method" => "POST",
        ]);
        
        $this->assertStatusCode(403, $client);
        $this->assertJsonResponse([
            "message" => "The login credentials are not valid."
        ], $client);
    }
}

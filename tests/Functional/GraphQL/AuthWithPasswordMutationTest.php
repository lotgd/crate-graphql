<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests\Functional\GraphQL;

use LotGD\Crate\GraphQL\Models\User;

class AuthWithPasswordMutationTest extends GraphQLTestCase
{
    public function testIfNonExistingUserFailsAuth()
    {
        $query = <<<EOF
mutation AuthWithPasswordMutation(\$input: AuthWithPasswordInput!) {
  authWithPassword(input: \$input) {
    apiKey
    expiresAt
  	clientMutationId
  }
}
EOF;
        
        $variables = <<<JSON
{
  "input": {
    "email": "A",
    "password": "12345",
    "clientMutationId":"avcd"
  }
}
JSON;
        
        $answer = <<<JSON
{
  "data": {
    "authWithPassword": null
  },
  "errors": [
    {
      "message": "The login credentials are invalid.",
      "locations": [
        {
          "line": 2,
          "column": 3
        }
      ]
    }
  ]
}
JSON;

        $this->assertQuery($query, $answer, $variables);
    }
    
    public function testIfKnownUserCanAuthenticate()
    {
        $query = <<<EOF
mutation AuthWithPasswordMutation(\$input: AuthWithPasswordInput!) {
  authWithPassword(input: \$input) {
    apiKey
    expiresAt
  	clientMutationId
  }
}
EOF;
        
        $variables = <<<JSON
{
  "input": {
    "email": "admin",
    "password": "12345",
    "clientMutationId":"avcd"
  }
}
JSON;
        
        $result = $this->getQueryResults($query, $variables);
        
        $this->assertArrayHasKey("data", $result);
        $this->assertArrayHasKey("authWithPassword", $result["data"]);
        $this->assertArrayHasKey("apiKey", $result["data"]["authWithPassword"]);
        $this->assertArrayHasKey("expiresAt", $result["data"]["authWithPassword"]);
        $this->assertArrayHasKey("clientMutationId", $result["data"]["authWithPassword"]);
        
        $this->assertSame("avcd", $result["data"]["authWithPassword"]["clientMutationId"]);
    }
    
    public function testIfTwoAuthRequestResultInSameApiKey()
    {
        $query = <<<EOF
mutation AuthWithPasswordMutation(\$input: AuthWithPasswordInput!) {
  authWithPassword(input: \$input) {
    apiKey
    expiresAt
  	clientMutationId
  }
}
EOF;
        
        $variables = <<<JSON
{
  "input": {
    "email": "admin",
    "password": "12345",
    "clientMutationId":"avcd"
  }
}
JSON;
        
        $result1 = $this->getQueryResults($query, $variables);
        $result2 = $this->getQueryResults($query, $variables);
        
        $this->assertSame($result1, $result2);
    }
}

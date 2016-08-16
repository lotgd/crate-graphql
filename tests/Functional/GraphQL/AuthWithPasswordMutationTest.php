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
}

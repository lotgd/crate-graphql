<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests\Functional\GraphQL;

use LotGD\Crate\GraphQL\Models\User;

class AuthWithPasswordMutationTest extends GraphQLTestCase
{
    public function testIfNonExistingUserFailsAuth()
    {
        $query = <<<'EOF'
mutation AuthWithPasswordMutation($input: AuthWithPasswordInput!) {
  authWithPassword(input: $input) {
    session {
        authToken,
        expiresAt,
        user {
            id,
            name
        }
    },
    clientMutationId,
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
      ],
      "path": [
        "authWithPassword"
      ],
      "category": "user"
    }
  ]
}
JSON;
        $this->assertQuery($query, $answer, $variables);
    }

    public function testIfKnownUserCanAuthenticate()
    {
        $query = <<<'EOF'
mutation AuthWithPasswordMutation($input: AuthWithPasswordInput!) {
  authWithPassword(input: $input) {
    session {
        authToken,
        expiresAt,
        user {
            id,
            name
        }
    },
    clientMutationId,
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
        $this->assertArrayHasKey("clientMutationId", $result["data"]["authWithPassword"]);
        $this->assertArrayHasKey("session", $result["data"]["authWithPassword"]);

        $this->assertArrayHasKey("authToken", $result["data"]["authWithPassword"]["session"]);
        $this->assertArrayHasKey("expiresAt", $result["data"]["authWithPassword"]["session"]);
        $this->assertArrayHasKey("user", $result["data"]["authWithPassword"]["session"]);
        $this->assertArrayHasKey("id", $result["data"]["authWithPassword"]["session"]["user"]);
        $this->assertArrayHasKey("name", $result["data"]["authWithPassword"]["session"]["user"]);

        $this->assertSame("avcd", $result["data"]["authWithPassword"]["clientMutationId"]);
        $this->assertGreaterThan(0, count($result["data"]["authWithPassword"]["session"]["user"]["name"]));
        $this->assertInternalType("string", $result["data"]["authWithPassword"]["session"]["user"]["id"]);
        $this->assertGreaterThan(0, count($result["data"]["authWithPassword"]["session"]["user"]["id"]));
    }

    public function testIfTwoAuthRequestResultInSameApiKey()
    {
        $query = <<<'EOF'
mutation AuthWithPasswordMutation($input: AuthWithPasswordInput!) {
  authWithPassword(input: $input) {
    session {
        authToken,
        expiresAt,
        user {
            id,
            name
        }
    },
    clientMutationId,
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

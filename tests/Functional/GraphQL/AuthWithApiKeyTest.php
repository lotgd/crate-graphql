<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests\Functional\GraphQL;

use LotGD\Crate\GraphQL\Models\User;

class AuthWithApiKeyTest extends GraphQLTestCase
{
    public function testWithValidAuthData()
    {
        // Step 1: Send request to properly authenticate as a user.
        $query = <<<'EOF'
mutation AuthWithPasswordMutation($input: AuthWithPasswordInput!) {
  authWithPassword(input: $input) {
    session {
        apiKey,
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

        $answer = $this->getQueryResults($query, $variables);

        $this->assertSame("avcd", $answer["data"]["authWithPassword"]["clientMutationId"]);
        $this->assertArrayHasKey("session", $answer["data"]["authWithPassword"]);

        $apiKey = $answer["data"]["authWithPassword"]["session"]["apiKey"];

        $query = <<<GraphQL
query RealmQuery {
    Realm {
        configuration {
            core {
                ...Lib
            }
            crate {
                ...Lib
            }
        }
    }
}

fragment Lib on Library {
    name
    version
    library
    url
    author
}
GraphQL;

        // Send authenticated request
        $client1 = $this->sendRequestToGraphQLEndpoint([
            "method" => "GET",
            "query" => [
                "query" => $query,
                "variables" => "{}",
            ],
            "server" => [
                "CONTENT_TYPE" => "application/graphql",
                "HTTP_TOKEN" => $apiKey,
            ]
        ]);

        // Send unauthenticated request
        $client2 = $this->sendRequestToGraphQLEndpoint([
            "method" => "GET",
            "query" => [
                "query" => $query,
                "variables" => "{}",
            ],
            "server" => [
                "CONTENT_TYPE" => "application/graphql",
            ]
        ]);

        $this->assertStatusCode(200, $client1);
        $this->assertStatusCode(200, $client2);
        $this->assertJsonStringEqualsJsonString($client2->getResponse()->getContent(), $client1->getResponse()->getContent());
    }

    public function testWithInvalidAuthData()
    {
        // Step 1: Send request to properly authenticate as a user.
        $query = <<<'EOF'
mutation AuthWithPasswordMutation($input: AuthWithPasswordInput!) {
  authWithPassword(input: $input) {
    session {
        apiKey,
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

        $answer = $this->getQueryResults($query, $variables);

        $this->assertSame("avcd", $answer["data"]["authWithPassword"]["clientMutationId"]);

        $apiKey = "HuLaLa";

        $query = <<<GraphQL
query RealmQuery {
    Realm {
        configuration {
            core {
                ...Lib
            }
            crate {
                ...Lib
            }
        }
    }
}

fragment Lib on Library {
    name
    version
    library
    url
    author
}
GraphQL;

        // Send authenticated request with wrong api Key
        $client = $this->sendRequestToGraphQLEndpoint([
            "method" => "GET",
            "query" => [
                "query" => $query,
                "variables" => "{}",
            ],
            "server" => [
                "CONTENT_TYPE" => "application/graphql",
                "HTTP_TOKEN" => $apiKey,
            ]
        ]);

        $this->assertStatusCode(401, $client);
        $this->assertJsonStringEqualsJsonString('{"error":["API Key \u0022HuLaLa\u0022 does not exist."]}', $client->getResponse()->getContent());
    }
}

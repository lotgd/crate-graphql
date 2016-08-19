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
        
        $answer = $this->getQueryResults($query, $variables);
        
        $this->assertSame("avcd", $answer["data"]["authWithPassword"]["clientMutationId"]);
        
        $apiKey = $answer["data"]["authWithPassword"]["apiKey"];
        
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
        
        $client = $this->sendRequestToGraphQLEndpoint([
            "method" => "GET",
            "query" => [
                "query" => $query,
                "variables" => "{}",
            ],
            "server" => [
                "CONTENT_TYPE" => "application/graphql",
                "X-Token" => $apiKey,
            ]
        ]);
    }
}

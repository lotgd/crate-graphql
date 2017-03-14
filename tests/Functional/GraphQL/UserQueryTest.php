<?php

namespace LotGD\Crate\GraphQL\Tests\Functional\GraphQL;

class UserQueryTest extends GraphQLTestCase
{
    public function testIfKnownUserCanBeRetrievedByName()
    {
        $query = <<<'GraphQL'
query UserQuery($name: String!) {
    user(name: $name) {
        id,
        name
    }
}
GraphQL;

        $jsonExpected = <<<JSON
{
    "data": {
        "user": {
            "id": "2",
            "name": "test-user"
        }
    }
}
JSON;

        $jsonVariables = <<<JSON
{
    "name": "test-user"
}
JSON;

        $this->assertQueryAuthenticated("c4fEAJLQlaV/47UZl52nAQ==", $query, $jsonExpected, $jsonVariables);
    }

    public function testIfKnownUserCannotBeRetrievedByNameWithoutProperAuthorization()
    {
        $query = <<<'GraphQL'
query UserQuery($name: String!) {
    user(name: $name) {
        id,
        name
    }
}
GraphQL;

        $jsonExpected = <<<JSON
{
    "data": {
        "user":null
    },
    "extensions": {
        "warnings": [{
            "message": "Access denied to this field.",
            "locations": [{"line":2,"column":5}],
            "path": ["user"]
        }]
    }
}
JSON;

        $jsonVariables = <<<JSON
{
    "name": "admin"
}
JSON;

        $this->assertQueryAuthenticated("c4fEAJLQlaV/47UZl52nAQ==", $query, $jsonExpected, $jsonVariables);
    }

    public function testIfAnUnknownUserRetrievedByNameReturnsStillAnAccessError()
    {
        $query = <<<'GraphQL'
query UserQuery($name: String!) {
    user(name: $name) {
        id,
        name
    }
}
GraphQL;

        $jsonExpected = <<<JSON
{
    "data": {
        "user":null
    },
    "extensions": {
        "warnings": [{
            "message": "Access denied to this field.",
            "locations": [{"line":2,"column":5}],
            "path": ["user"]
        }]
    }
}
JSON;

        $jsonVariables = <<<JSON
{
    "name": "admins"
}
JSON;

        $this->assertQueryAuthenticated("c4fEAJLQlaV/47UZl52nAQ==", $query, $jsonExpected, $jsonVariables);
    }

    public function testIfCharacterFieldsReturnAValidListOfCharacters()
    {
        $query = <<<'GraphQL'
query UserQuery($id: String!) {
    user(id: $id) {
        id,
        name,
        characters {
            id,
            name
            displayName
        }
    }
}
GraphQL;

        $jsonVariables = <<<JSON
{
    "id": "2"
}
JSON;

        $jsonExpected = <<<JSON
{
	"data": {
		"user": {
			"id": "2",
			"name": "test-user",
			"characters": [{
                "id": "1",
                "name": "DB-Test",
                "displayName": "Novice DB-Test"
			}]
		}
	}
}
JSON;

        $this->assertQueryAuthenticated("c4fEAJLQlaV/47UZl52nAQ==", $query, $jsonExpected, $jsonVariables);
    }
}
<?php

namespace LotGD\Crate\GraphQL\Tests\Functional\GraphQL;

class UserQueryTest extends GraphQLTestCase
{
    public function testIfKnownUserCanBeRetrieved()
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
            "id": "1",
            "name": "admin"
        }
    }
}
JSON;

        $jsonVariables = <<<JSON
{
    "name": "admin"
}
JSON;

        $this->assertQuery($query, $jsonExpected, $jsonVariables);
    }

    public function testIfUnknownUserReturnsNull()
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
    "user": null
  }
}
JSON;

        $jsonVariables = <<<JSON
{
    "name": "admins"
}
JSON;

        $this->assertQuery($query, $jsonExpected, $jsonVariables);
    }
}
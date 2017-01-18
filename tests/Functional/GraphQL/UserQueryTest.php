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

    public function testIfAnUnknownUserRetrievedByNameReturnsNull()
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
    
    public function testIfCharacterFieldsReturnAValidListOfCharacters()
    {
        $query = <<<'GraphQL'
query UserQuery($id: String!) {
    user(id: $id) {
        id,
        name,
        characters {
            id,
            name,
            displayName
        }
    }
}
GraphQL;
        
        $jsonVariables = <<<JSON
{
    "id": "1"
}
JSON;
        
        $jsonExpected = <<<JSON
{
  "data": {
    "user": {
      "id": "1",
      "name": "admin",
      "characters": [
        {"id": "2", "name": "One", "displayName": "The One And Only"}
      ]
    }
  }
}
JSON;
        
        $this->assertQuery($query, $jsonExpected, $jsonVariables);
    }
}
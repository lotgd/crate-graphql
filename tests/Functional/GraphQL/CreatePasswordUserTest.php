<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests\Functional\GraphQL;

use LotGD\Crate\GraphQL\Models\User;

class CreatePasswordUserTest extends GraphQLTestCase
{
    public function testCreationOfValidUser()
    {
        $query = <<<'EOF'
mutation CreatePasswordUserMutation($input: CreatePasswordUserInput!) {
  createPasswordUser(input: $input) {
    clientMutationId
  }
}
EOF;
        
        $variables = <<<JSON
{
  "input": {
    "name": "test",
    "email": "email@example.com",
    "password": "test",
    "clientMutationId": "ijhdioasd"
  }
}
JSON;
        
        $answer = <<<JSON
{
  "data": {
    "createPasswordUser": {
      "clientMutationId": "ijhdioasd"
    }
  }
}
JSON;

        $this->assertQuery($query, $answer, $variables);
    }
    
    public function testErrorIfUserNameIsAlreadyInUse()
    {
        $query = <<<'EOF'
mutation CreatePasswordUserMutation($input: CreatePasswordUserInput!) {
  createPasswordUser(input: $input) {
    clientMutationId
  }
}
EOF;
        
        $variables1 = <<<JSON
{
  "input": {
    "name": "testErrorIfUserNameIsAlreadyInUse_1",
    "email": "testErrorIfUserNameIsAlreadyInUse_1@example.com",
    "password": "test",
    "clientMutationId": "ijhdioasd"
  }
}
JSON;
        
        $answer1 = <<<JSON
{
  "data": {
    "createPasswordUser": {
      "clientMutationId": "ijhdioasd"
    }
  }
}
JSON;
        
        $variables2 = <<<JSON
{
  "input": {
    "name": "testErrorIfUserNameIsAlreadyInUse_1",
    "email": "testErrorIfUserNameIsAlreadyInUse_2@example.com",
    "password": "test",
    "clientMutationId": "ijhdioasd"
  }
}
JSON;
        
        $answer2 = <<<JSON
{
  "data": {
    "createPasswordUser": null
  },
  "errors": [
    {
      "message": "Username is already in use.",
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

        $this->assertQuery($query, $answer1, $variables1);
        $this->assertQuery($query, $answer2, $variables2);
    }
    
    public function testErrorIfEmailIsAlreadyInUse()
    {
        $query = <<<'EOF'
mutation CreatePasswordUserMutation($input: CreatePasswordUserInput!) {
  createPasswordUser(input: $input) {
    clientMutationId
  }
}
EOF;
        
        $variables1 = <<<JSON
{
  "input": {
    "name": "testErrorIfEmailIsAlreadyInUse_1",
    "email": "testErrorIfEmailIsAlreadyInUse_1@example.com",
    "password": "test",
    "clientMutationId": "ijhdioasd"
  }
}
JSON;
        
        $answer1 = <<<JSON
{
  "data": {
    "createPasswordUser": {
      "clientMutationId": "ijhdioasd"
    }
  }
}
JSON;
        
        $variables2 = <<<JSON
{
  "input": {
    "name": "testErrorIfEmailIsAlreadyInUse_2",
    "email": "testErrorIfEmailIsAlreadyInUse_1@example.com",
    "password": "test",
    "clientMutationId": "ijhdioasd"
  }
}
JSON;
        
        $answer2 = <<<JSON
{
  "data": {
    "createPasswordUser": null
  },
  "errors": [
    {
      "message": "Email address is already in use.",
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

        $this->assertQuery($query, $answer1, $variables1);
        $this->assertQuery($query, $answer2, $variables2);
    }
}

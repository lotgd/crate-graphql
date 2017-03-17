<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests\Functional\GraphQL;

/**
 * Description of CharacterMutationTest
 */
class CharacterMutationTest extends GraphQLTestCase
{
    protected function getSimpleCreationMutation()
    {
        return <<<'GraphQL'
mutation createCharacterMutation($input: CreateCharacterInput!) {
    createCharacter(input: $input) {
        user {
            id
        },
        character {
            id,
            name,
            displayName
        }
    }
}
GraphQL;
    }

    protected function getSimpleCreationMutationInput(int $id, string $name, string $mutationId)
    {
        return <<<JSON
{
    "input": {
        "clientMutationId": "$mutationId",
        "userId": "$id",
        "characterName": "$name"
    }
}
JSON;
    }

    public function testIfCharacterCreationWorksIfAuthenticated()
    {
        $mutation = $this->getSimpleCreationMutation();
        $variables = $this->getSimpleCreationMutationInput(2, "New Player", "asd789g7");

        $result = $this->getQueryResultsAuthorized("c4fEAJLQlaV/47UZl52nAQ==", $mutation, $variables);

        $expectedReturn = [
            "data" => [
                "createCharacter" => [
                    "user" => [
                        "id" => "2"
                    ],
                    "character" => [
                        "id" => $result["data"]["createCharacter"]["character"]["id"],
                        "name" => "New Player",
                        "displayName" => "New Player"
                    ]
                ]
            ]
        ];

        $this->assertSame($expectedReturn, $result);
    }

    public function testIfCharacterCreationFailsIfNameIsAlreadyUsed()
    {
        $mutation = $this->getSimpleCreationMutation();
        $variables = $this->getSimpleCreationMutationInput(2, "One", "asd789g7");

        $answer = <<<JSON
{
    "data": {
        "createCharacter": null
    },
    "errors": [
        {
            "message": "Character with name One already taken.",
            "locations": [
                {
                    "line": 2,
                    "column": 5
                }
            ],
            "path": [
                "createCharacter"
            ]
        }
    ]
}
JSON;

        $this->assertQueryAuthorized("c4fEAJLQlaV/47UZl52nAQ==", $mutation, $answer, $variables);
    }

    public function testIfCharacterCreationFailsIfUserIsNotLoggedIn()
    {
        $mutation = $this->getSimpleCreationMutation();
        $variables = $this->getSimpleCreationMutationInput(2, "One", "asd789g7");

        $answer = <<<JSON
{
    "data": {
        "createCharacter": null
    },
    "errors": [
        {
            "message": "Access denied for this mutation.",
            "locations": [
                {
                    "line": 2,
                    "column": 5
                }
            ],
            "path": [
                "createCharacter"
            ]
        }
    ]
}
JSON;

        $this->assertQuery($mutation, $answer, $variables);
    }

    public function testIfCharacterCreationFailsIfUserTriesToCreateOneForAnotherUser()
    {
        $mutation = $this->getSimpleCreationMutation();
        $variables = $this->getSimpleCreationMutationInput(1, "Another New Character", "asd789g7");

        $answer = <<<JSON
{
    "data": {
        "createCharacter": null
    },
    "errors": [
        {
            "message": "Access denied for this mutation.",
            "locations": [
                {
                    "line": 2,
                    "column": 5
                }
            ],
            "path": [
                "createCharacter"
            ]
        }
    ]
}
JSON;

        $this->assertQueryAuthorized("c4fEAJLQlaV/47UZl52nAQ==", $mutation, $answer, $variables);

    }

    public function testIfCharacterCreationWorksIfUserTriesToCreateOneForAnotherUserButIsSuperuser()
    {
        $mutation = $this->getSimpleCreationMutation();
        $variables = $this->getSimpleCreationMutationInput(1, "Another New Character", "asd789g7");

        $result = $this->getQueryResultsAuthorized("apiKeyForUser3", $mutation, $variables);

        $expectedReturn = [
            "data" => [
                "createCharacter" => [
                    "user" => [
                        "id" => "1"
                    ],
                    "character" => [
                        "id" => $result["data"]["createCharacter"]["character"]["id"],
                        "name" => "Another New Character",
                        "displayName" => "Another New Character"
                    ]
                ]
            ]
        ];

        $this->assertSame($expectedReturn, $result);
    }
}

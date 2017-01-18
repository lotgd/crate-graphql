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
        character {
            name
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

    public function testIfCharacterCreationWorks()
    {
        $mutation = $this->getSimpleCreationMutation();
        $variables = $this->getSimpleCreationMutationInput(1, "New Player", "asd789g7");
        $expectedReturn = [
            "data" => [
                "createCharacter" => [
                    "character" => [
                        "name" => "New Player",
                        "displayName" => "New Player"
                    ]
                ]
            ]
        ];

        $this->assertQuery($mutation, json_encode($expectedReturn), $variables);
    }
}

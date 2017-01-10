<?php

namespace LotGD\Crate\GraphQL\Tests\Functional\GraphQL;

class CharacterQueryTest extends GraphQLTestCase
{
    public function testIfCharacterQueryWithoutArgumentsReturnsNull()
    {
        $query = <<<'EOF'
query CharacterQuery {
    character {
        id
        name
        displayName
    }
}
EOF;
        
        $jsonExpected = <<<JSON
{
    "data": {
        "character": null
    }
}
JSON;

        $this->assertQuery($query, $jsonExpected);
    }
    
    public function testIfCharacterQueryWithIdReturnsCorrectCharacter()
    {
        $query = <<<'EOF'
query CharacterQuery($id: String) {
    character(characterId: $id) {
        id
        name
        displayName
    }
}
EOF;
        
        $jsonVariables = <<<JSON
{
    "id": "1"
}
JSON;
        
        $jsonExpected = <<<JSON
{
    "data": {
        "character": {
            "id": "1",
            "name": "DB-Test",
            "displayName": "DB-Test"
        }
    }
}
JSON;

        $this->assertQuery($query, $jsonExpected, $jsonVariables);
    }
    
    public function testIfCharacterQueryWithNameReturnsCorrectCharacter()
    {
        $query = <<<'EOF'
query CharacterQuery($name: String) {
    character(characterName: $name) {
        id
        name
        displayName
    }
}
EOF;
        
        $jsonVariables = <<<JSON
{
    "name": "One"
}
JSON;
        
        $jsonExpected = <<<JSON
{
    "data": {
        "character": {
            "id": "2",
            "name": "One",
            "displayName": "The One And Only"
        }
    }
}
JSON;

        $results = $this->getQueryResults($query, $jsonVariables);
        $this->assertQuery($query, $jsonExpected, $jsonVariables);
    }
}

<?php

namespace LotGD\Crate\GraphQL\Tests\Functional\GraphQL;

use Doctrine\Common\Util\Debug;
use LotGD\Core\EventHandler;
use LotGD\Core\Events\EventContext;
use LotGD\Core\Game;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\CharacterStatIntType;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Types\CharacterStatRangeType;


class TestEventProvider implements EventHandler
{
    public static function handleEvent(Game $g, EventContext $context): EventContext
    {
        $stats = $context->getDataField("value");
        $character = $context->getDataField("character");

        $stats = array_merge($stats, [
            new CharacterStatIntType("lotgd/core/level", "Level", $character->getLevel()),
            new CharacterStatIntType("lotgd/core/attack", "Attack", $character->getAttack()),
            new CharacterStatIntType("lotgd/core/defense", "Defense", $character->getDefense()),
            new CharacterStatRangeType("lotgd/core/health", "Health", $character->getHealth(), $character->getMaxHealth()),
        ]);

        $context->setDataField("value", $stats);
        return $context;
    }
}

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
        level
        attack
        defense
        health
        maxHealth
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
        level
        attack
        defense
        health
        maxHealth
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
            "displayName": "Novice DB-Test",
            "level": 1,
            "attack": 1,
            "defense": 1,
            "health": 10,
            "maxHealth": 10
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

    public function testIfQueryOnCharacterStatsReturnsStats()
    {
        $query = <<<'EOF'
query CharacterQuery($name: String) {
    character(characterName: $name) {
        id
        publicStats {
            id
            name
            type
            
            ... on CharacterStatInt {
                value
            }
            
            ... on CharacterStatRange {
                currentValue
                maxValue
            }
        }
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
            "publicStats": [{
                "id":"lotgd\/core\/level",
                "name":"Level", 
                "type":"CharacterStatInt",
                "value":100
            }, {
                "id":"lotgd\/core\/attack",
                "name":"Attack",
                "type":"CharacterStatInt",
                "value":100
            }, {
                "id":"lotgd\/core\/defense",
                "name":"Defense",
                "type":"CharacterStatInt",
                "value":100
            },{
                "id":"lotgd\/core\/health",
                "name":"Health",
                "type":"CharacterStatRange",
                "currentValue":1000,
                "maxValue":1000
            }]
        }
    }
}
JSON;

        /** @var Game $game */
        $game = self::$game;
        $game->getEventManager()->subscribe("#h/lotgd/crate-graphql/characterStats/public#", TestEventProvider::class, "lotgd/test");

        $results = $this->getQueryResults($query, $jsonVariables);
        $this->assertQuery($query, $jsonExpected, $jsonVariables);

        $game->getEventManager()->unsubscribe("#h/lotgd/crate-graphql/characterStats/public#", TestEventProvider::class, "lotgd/test");
    }

    public function testIfQueryOnPrivateCharacterStatsReturnsNothingIfNotAuthenticated()
    {
        $query = <<<'EOF'
query CharacterQuery($name: String) {
    character(characterName: $name) {
        id
        privateStats {
            id
            name
            type
            
            ... on CharacterStatInt {
                value
            }
            
            ... on CharacterStatRange {
                currentValue
                maxValue
            }
        }
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
            "privateStats": null
        }
    }
}
JSON;

        /** @var Game $game */
        $game = self::$game;
        $game->getEventManager()->subscribe("#h/lotgd/crate-graphql/characterStats/public#", TestEventProvider::class, "lotgd/test");

        $results = $this->getQueryResults($query, $jsonVariables);
        $this->assertQuery($query, $jsonExpected, $jsonVariables);

        $game->getEventManager()->unsubscribe("#h/lotgd/crate-graphql/characterStats/public#", TestEventProvider::class, "lotgd/test");
    }

    public function testIfQueryOnPrivateCharacterStatsReturnsPublicStatsIfProperlyAuthenticated()
    {
        $query = <<<'EOF'
query CharacterQuery($name: String) {
    character(characterName: $name) {
        id
        privateStats {
            id
            name
            type
            
            ... on CharacterStatInt {
                value
            }
            
            ... on CharacterStatRange {
                currentValue
                maxValue
            }
        }
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
            "privateStats": null
        }
    }
}
JSON;

        /** @var Game $game */
        $game = self::$game;
        $game->getEventManager()->subscribe("#h/lotgd/crate-graphql/characterStats/public#", TestEventProvider::class, "lotgd/test");

        $results = $this->getQueryResultsAuthorized("c4fEAJLQlaV/47UZl52nAQ==", $query, $jsonVariables);
        $this->assertQuery($query, $jsonExpected, $jsonVariables);

        $game->getEventManager()->unsubscribe("#h/lotgd/crate-graphql/characterStats/public#", TestEventProvider::class, "lotgd/test");
    }
}

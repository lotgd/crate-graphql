<?php

namespace LotGD\Crate\GraphQL\Tests\Functional\GraphQL;

class RealmQueryTest extends GraphQLTestCase
{
    public function testRealmTypeReturnsGeneralInformation()
    {
        $query = <<<EOF
query RealmQuery {
    Realm {
        name
    }
}
EOF;

        $this->assertArrayKeysInQuery($query, "Realm", ["name"]);
    }

    public function testIfRealmReturnsCrateAndCore()
    {
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

        $result = $this->getQueryResults($query);

        $this->assertArrayHasKey("data", $result);
        $this->assertArrayHasKey("Realm", $result["data"]);
        $this->assertArrayHasKey("configuration", $result["data"]["Realm"]);
        $this->assertArrayHasKey("core", $result["data"]["Realm"]['configuration']);
        $this->assertArrayHasKey("crate", $result["data"]["Realm"]['configuration']);
    }
}

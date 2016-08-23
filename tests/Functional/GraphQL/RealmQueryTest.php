<?php

namespace LotGD\Crate\GraphQL\Tests\Functional\GraphQL;

class RealmQueryTest extends GraphQLTestCase
{
    public function testRealmTypeReturnsGeneralInformation()
    {
        $query = <<<EOF
query RealmQuery {
    realm {
        name
    }
}
EOF;

        $this->assertArrayKeysInQuery($query, "realm", ["name"]);
    }

    public function testIfRealmReturnsCrateAndCore()
    {
        $query = <<<GraphQL
query RealmQuery {
    realm {
        configuration {
            core {
                ...LibraryFragment
            }
            crate {
                ...LibraryFragment
            }
        }
    }
}
                
fragment LibraryFragment on Library {
    name
    version
    library
    url
    author
}
GraphQL;

        $result = $this->getQueryResults($query);

        $this->assertArrayHasKey("data", $result);
        $this->assertArrayHasKey("realm", $result["data"]);
        $this->assertArrayHasKey("configuration", $result["data"]["realm"]);
        $this->assertArrayHasKey("core", $result["data"]["realm"]['configuration']);
        $this->assertArrayHasKey("crate", $result["data"]["realm"]['configuration']);
    }
}

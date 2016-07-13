<?php

namespace Tests\AppBundle\Functional;

use Tests\AppBundle\Functional\WebTestCase;

class RealmQueryTest extends WebTestCase
{
    public function testRealmTypeReturnsGeneralInformations()
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

    public function testIfRealmReturnsLibraryList()
    {
        $query = <<<GraphQL
query RealmQuery {
    Realm {
        configuration {
            core {
                name
                version
                library
                url
                author
            }
            crate {
                name
                version
                library
                url
                author
            }
        }
    }
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

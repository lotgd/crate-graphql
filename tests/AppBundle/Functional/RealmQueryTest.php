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
        libraries {
            name
            version
            library
            url
            author
        }
    }
}
GraphQL;
        
        $result = $this->getQueryResults($query);
        
        $this->assertArrayHasKey("data", $result);
        $this->assertArrayHasKey("Realm", $result["data"]);
        $this->assertArrayHasKey("libraries", $result["data"]["Realm"]);
        $this->assertGreaterThanOrEqual(2, count($result["data"]["Realm"]["libraries"]));
    }
}

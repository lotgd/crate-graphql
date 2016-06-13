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
        $jsonExpected = <<<JSON
{
    "data": {
        "Realm": {
            "libraries": [
                {
                    "name": "Core",
                    "version": "0.1.0",
                    "library": "lotgd\/core",
                    "url": "https:\/\/github.com\/lotgd\/core.git",
                    "author": "The daenerys development team"
                }, {
                    "name": "Crate",
                    "version": "0.1.0",
                    "library": "lotgd\/crate-www",
                    "url": "https:\/\/github.com\/lotgd\/crate-www.git",
                    "author": "The daenerys development team"
                }
            ]
        }
    }
}
JSON;
        
        $this->assertQuery($query, $jsonExpected);
    }
}

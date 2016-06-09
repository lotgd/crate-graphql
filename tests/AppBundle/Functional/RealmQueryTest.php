<?php

namespace Tests\AppBundle\Functional\Relay;

use Tests\AppBundle\Functional\WebTestCase;

class RealmQueryTest extends WebTestCase
{
    public function testRealmTypeReturnsGeneralInformations()
    {
        $query = <<<EOF
query RealmQuery {
    Realm {
        name,
        crateVersion,
        coreVersion
    }
}
EOF;
        
        $this->assertArrayKeysInQuery($query, "Realm", ["coreVersion", "crateVersion", "name"]);
    }
}

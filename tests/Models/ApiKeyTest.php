<?php

namespace LotGD\Crate\GraphQL\Tests;

use DateInterval;

use LotGD\Crate\GraphQL\Tests\WebTestCase;

use LotGD\Crate\GraphQL\Models\ApiKey;
use LotGD\Crate\GraphQL\Models\User;

class ApiKeyTest extends WebTestCase
{
    public function testApiKeyGeneration()
    {
        $em = $this->getEntityManager();
        $user = $em->getRepository(User::class)->find(1);
        
        $this->assertFalse($user->hasApiKey());
        
        $key = ApiKey::generate($user, 10);
        $user->setApiKey($key);
        
        $this->assertTrue($user->hasApiKey());
        $this->assertTrue($key->isValid());
        
        $created = $key->getCreatedAt();
        $expires = $key->getExpiresAt();
        
        $created2 = $created->add(DateInterval::createFromDateString("10 seconds"));
        
        $this->assertEquals(
            $created2->format("Ymd-His"),
            $expires->format("Ymd-His")
        );
        
        $em->clear();
    }
}

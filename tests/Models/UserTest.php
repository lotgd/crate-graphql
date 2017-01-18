<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests;

use LotGD\Crate\GraphQL\Models\User;
use LotGD\Crate\GraphQL\Models\ApiKey;
use LotGD\Core\Models\Character;

class UserTest extends WebTestCase
{
    private $name = "test";
    private $mail = "mail@example.com";
    private $pass = "password";
    private $wrongPass = "wrongPassword";
    private $newPass = "newPassword";
    
    /**
     * Returns a basic User to do some tests
     * @return User
     */
    private function getTestUser(): User
    {
        return new User($this->name, $this->mail, $this->pass);
    }
    
    public function testGetters()
    {
        $user = $this->getTestUser();
        
        $this->assertSame($this->name, $user->getName());
        $this->assertSame($this->mail, $user->getEmail());
    }
    
    public function testIfPasswordVerificationWorks()
    {
        $user = $this->getTestUser();
        
        $this->assertFalse($user->verifyPassword($this->wrongPass));
        $this->assertTrue($user->verifyPassword($this->pass));
    }
    
    public function testIfChangingPasswordWorks()
    {
        $user = $this->getTestUser();
        $user->setPassword($this->newPass);
        
        $this->assertFalse($user->verifyPassword($this->pass));
        $this->assertTrue($user->verifyPassword($this->newPass));
    }
    
    public function testApiKey()
    {
        $user = $this->getTestUser();
        $apiKey = $this->getMockBuilder(ApiKey::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        // No api key set so far - assert false
        $this->assertFalse($user->hasApiKey());   
        
        // Set key
        $user->setApiKey($apiKey);
        
        // Now test again
        $this->assertTrue($user->hasApiKey());
        $this->assertSame($apiKey, $user->getApiKey());
    }
    
    public function testIfUsersListAllItsCharacters()
    {
        $user = $this->getEntityManager()->getRepository(User::class)->find(1);
        
        $i = 0;
        foreach ($user->fetchCharacters() as $character) {
            $this->assertInstanceOf(Character::class, $character);
            $i++;
        }
        $this->assertSame(1, $i);
    }
    
    public function testIfUserHasCharacterReturnsTrueWhenItShould()
    {
        $user = $this->getEntityManager()->getRepository(User::class)->find(1);
        $character = $this->getEntityManager()->getRepository(Character::class)->find(2);
        
        $this->assertTrue($user->hasCharacter($character));
    }
    
    public function testIfUserHasCharacterReturnsFalseWhenItShould()
    {
        $user = $this->getEntityManager()->getRepository(User::class)->find(1);
        $character = $this->getEntityManager()->getRepository(Character::class)->find(1);
        
        $this->assertFalse($user->hasCharacter($character));
    }
}

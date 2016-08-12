<?php

namespace LotGD\Crate\GraphQL\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;
use PHPUnit_Extensions_Database_DataSet_YamlDataSet;

class WebTestCase extends BaseWebTestCase
{
    static $em;
    
    public function setUp()
    {
        if (static::$em === null) {
            static::$kernel = static::createKernel();
            static::$kernel->boot();
                    
            static::$em = static::$kernel->getContainer()->get("lotgd.core.game")->getEntityManager();
            $this->loadDefaultData(static::$em);
        }
    }
    
    public function loadDefaultData(EntityManagerInterface $em)
    {
        include implode(DIRECTORY_SEPARATOR, [getcwd(), "tests", "datasets", "default.php"]);
        $em->flush();
        $em->clear();
    }
    
    protected function getEntityManager(): EntityManagerInterface
    {
        return static::$em;
    }
    
    protected function sendRequest($path, array $requestData = [])
    {
        $client = static::makeClient();
        
        $method = $requestData["method"] ?? "GET";
        $query = $requestData["query"] ?? [];
        $files = $request["files"] ?? [];
        $server = $request["server"] ?? [];
        $content = $request["content"] ?? null;
        
        $client->request($method, $path, $query, $files, $server, $content);
        
        return $client;
    }
    
    protected function assertValidJson($client)
    {
        $content = $client->getResponse()->getContent();
        $type = $client->getResponse()->headers->get("Content-Type");
        
        $this->assertJson($content);
        $this->assertSame("application/json", $type);
    }
    
    protected function assertJsonResponse($expected, $client)
    {
        $this->assertValidJson($client);
        
        $content = $client->getResponse()->getContent();
        $this->assertSame($expected, json_decode($content, true));
    }
    
    protected function assertJsonContainsKeys($expectedKeys, $client)
    {
        $jsonArray = json_decode($client->getResponse()->getContent(), true);
                
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $jsonArray);
        }
    }
    
    
    protected function queryHelper($query, $jsonVariables)
    {
        $client = static::makeClient();
        $path = $this->getUrl("lotgd_crate_graphql_app_graph_endpoint");

        $client->request(
            'GET', $path, ['query' => $query, 'variables' => $jsonVariables], [], ['CONTENT_TYPE' => 'application/graphql']
        );
        $result = $client->getResponse()->getContent();
        
        return [$result, $client];
    }
    
    protected function getQueryResults($query, $jsonVariables = '{}')
    {
        list($result, $client) = $this->queryHelper($query, $jsonVariables);
        
        $this->assertStatusCode(200, $client);
        
        return json_decode($result, true);
    }

    protected function assertQuery($query, $jsonExpected, $jsonVariables = '{}')
    {
        list($result, $client) = $this->queryHelper($query, $jsonVariables);
        
        $this->assertStatusCode(200, $client);
        $this->assertEquals(json_decode($jsonExpected, true), json_decode($result, true), $result);
    }
    
    protected function assertArrayKeysInQuery($query, $subKey, $arrayKeys, $jsonVariables = "{}")
    {
        list($result, $client) = $this->queryHelper($query, $jsonVariables);
        
        $this->assertStatusCode(200, $client);
        
        $resultArray = json_decode($result, true);
        
        $this->assertArrayHasKey("data", $resultArray);
        $this->assertArrayHasKey($subKey, $resultArray["data"]);
        foreach ($arrayKeys as $key) {
            $this->assertArrayHasKey($key, $resultArray["data"][$subKey]);
        }
    }
}

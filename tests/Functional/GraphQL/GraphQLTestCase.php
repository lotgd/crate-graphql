<?php

namespace LotGD\Crate\GraphQL\Tests\Functional\GraphQL;

use Doctrine\ORM\Tools\SchemaTool;
use LotGD\Crate\GraphQL\Tests\WebTestCase;

class GraphQLTestCase extends WebTestCase
{    
    protected function sendRequestToGraphQLEndpoint(array $requestData = [])
    {
        $client = static::makeClient();
        
        $method = $requestData["method"] ?? "GET";
        $query = $requestData["query"] ?? [];
        $files = $requestData["files"] ?? [];
        $server = $requestData["server"] ?? [];
        $content = $requestData["content"] ?? null;
        
        $client->request(
            $method, 
            $this->getUrl("lotgd_crate_graphql_app_graph_endpoint"), 
            $query, 
            $files, 
            $server, 
            $content
        );
        
        return $client;
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

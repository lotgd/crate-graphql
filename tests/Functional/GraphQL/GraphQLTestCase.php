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
            $this->getUrl("overblog_graphql_endpoint"),
            $query, 
            $files, 
            $server, 
            $content
        );
        
        return $client;
    }

    protected function queryHelper($query, $jsonVariables, $apiKey = null)
    {
        $client = static::makeClient();
        $path = $this->getUrl("overblog_graphql_endpoint");

        $headers = ['CONTENT_TYPE' => 'application/graphql'];
        if ($apiKey) {
            $headers["HTTP_X_LOTGD_AUTH_TOKEN"] = $apiKey;
        }

        $client->request(
            'GET', $path, ['query' => $query, 'variables' => $jsonVariables], [], $headers
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

    protected function getQueryResultsAuthorized($apiKey, $query, $jsonVariables = '{}')
    {
        list($result, $client) = $this->queryHelper($query, $jsonVariables, $apiKey);

        $this->assertStatusCode(200, $client);

        return json_decode($result, true);
    }

    protected function assertQuery($query, $jsonExpected, $jsonVariables = '{}')
    {
        list($result, $client) = $this->queryHelper($query, $jsonVariables);
        
        $this->assertStatusCode(200, $client);
        $result_decoded = json_decode($result, true);

        if (isset($result_decoded["errors"])) {
            foreach ($result_decoded["errors"] as $key => $error) {
                unset($result_decoded["errors"][$key]["trace"]);
            }
        }

        $this->assertEquals(json_decode($jsonExpected, true), $result_decoded, $result);
    }

    protected function assertQueryAuthorized($apiKey, $query, $jsonExpected, $jsonVariables = '{}')
    {
        list($result, $client) = $this->queryHelper($query, $jsonVariables, $apiKey);

        $this->assertStatusCode(200, $client);

        $result_decoded = json_decode($result, true);
        if (isset($result_decoded["errors"])) {
            foreach ($result_decoded["errors"] as $key => $error) {
                unset($result_decoded["errors"][$key]["trace"]);
            }
        }

        $this->assertEquals(json_decode($jsonExpected, true), $result_decoded, $result);
    }

    protected function assertQueryResult($expected, $result) {
        $this->assertEquals(json_decode($expected, true), $result);
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

<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests\Functional\GraphQL;

/**
 * Tests setting data via a viewpoint mutation.
 */
class ViewpointMutationTest extends GraphQLTestCase
{
    public function testTakeActionMutation()
    {
        // First, get current viewpoint.
        $query = <<<'GraphQL'
query ViewQuery($id: String!) {
  viewpoint(characterId: $id) {
    title,
    description,
    template,
    actions {
      id,
      title,
      sortKey,
      actions {
        id,
        title,
      }
    }
  }
}
GraphQL;

        $jsonVariables = <<<JSON
{
    "id": "1"
}
JSON;
        
        $results = $this->getQueryResults($query, $jsonVariables);
        
        $takeActionId = $results["data"]["viewpoint"]["actions"][0]["actions"][0]["id"];
        
        $mutation = <<<'GraphQL'
mutation takeActionMutation($input: TakeActionInput!) {
  takeAction(input: $input) {
    viewpoint {
      title,
      description,
      template,
      actions {
        id,
        title,
        sortKey,
        actions {
          id,
          title,
        }
      }
    }
  }
}
GraphQL;
        
        $mutationVariables = <<<JSON
{
    "input": {
        "clientMutationId": "mutationId", 
        "characterId": 1, 
        "actionId": "$takeActionId"
    }
}
JSON;
        
        $results2 = $this->getQueryResults($mutation, $mutationVariables);
        $this->assertArrayHasKey("viewpoint", $results2["data"]["takeAction"]);
        $this->assertArrayHasKey("title", $results2["data"]["takeAction"]["viewpoint"]);
        $this->assertArrayHasKey("description", $results2["data"]["takeAction"]["viewpoint"]);
        $this->assertArrayHasKey("template", $results2["data"]["takeAction"]["viewpoint"]);
        $this->assertArrayHasKey("actions", $results2["data"]["takeAction"]["viewpoint"]);
        $this->assertGreaterThan(0, count($results2["data"]["takeAction"]["viewpoint"]["actions"]));
        $this->assertGreaterThan(0, count($results2["data"]["takeAction"]["viewpoint"]["actions"][0]["actions"]));
    }
}

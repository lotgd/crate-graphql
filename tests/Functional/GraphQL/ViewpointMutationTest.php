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
    actionGroups {
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

        $results = $this->getQueryResultsAuthenticated("c4fEAJLQlaV/47UZl52nAQ==", $query, $jsonVariables);
        $this->assertArrayNotHasKey("errors", $results);

        $takeActionId = $results["data"]["viewpoint"]["actionGroups"][0]["actions"][0]["id"];

        // Then, mutate the current viewpoint by taking an action.
        $mutation = <<<'GraphQL'
mutation takeActionMutation($input: TakeActionInput!) {
  takeAction(input: $input) {
    viewpoint {
      title,
      description,
      template,
      actionGroups {
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

        // We cannot assert the whole query since actionId is randomly generated.
        $results2 = $this->getQueryResultsAuthenticated("c4fEAJLQlaV/47UZl52nAQ==", $mutation, $mutationVariables);
        $this->assertArrayNotHasKey("errors", $results2);

        $this->assertArrayHasKey("viewpoint", $results2["data"]["takeAction"]);
        $this->assertArrayHasKey("title", $results2["data"]["takeAction"]["viewpoint"]);
        $this->assertArrayHasKey("description", $results2["data"]["takeAction"]["viewpoint"]);
        $this->assertArrayHasKey("template", $results2["data"]["takeAction"]["viewpoint"]);
        $this->assertArrayHasKey("actionGroups", $results2["data"]["takeAction"]["viewpoint"]);
        $this->assertGreaterThan(0, count($results2["data"]["takeAction"]["viewpoint"]["actionGroups"]));
        $this->assertGreaterThan(0, count($results2["data"]["takeAction"]["viewpoint"]["actionGroups"][0]["actions"]));
    }
}

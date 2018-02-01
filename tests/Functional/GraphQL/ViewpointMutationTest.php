<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests\Functional\GraphQL;

/**
 * Tests setting data via a viewpoint mutation.
 */
class ViewpointMutationTest extends GraphQLTestCase
{
    protected function getCurrentViewpointActionId(int $characterId = 1)
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
    "id": "$characterId"
}
JSON;

        // Let us do this as administrator
        $results = $this->getQueryResultsAuthorized("apiKeyForUser3", $query, $jsonVariables);
        $this->assertArrayNotHasKey("errors", $results);

        return $results["data"]["viewpoint"]["actionGroups"][0]["actions"][0]["id"];
    }

    public function testTakeActionMutationWhileAuthorized()
    {
        $takeActionId = $this->getCurrentViewpointActionId();

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
        "characterId": "1",
        "actionId": "$takeActionId"
    }
}
JSON;

        // We cannot assert the whole query since actionId is randomly generated.
        $results2 = $this->getQueryResultsAuthorized("c4fEAJLQlaV/47UZl52nAQ==", $mutation, $mutationVariables);
        $this->assertArrayNotHasKey("errors", $results2);

        $this->assertArrayHasKey("viewpoint", $results2["data"]["takeAction"]);
        $this->assertArrayHasKey("title", $results2["data"]["takeAction"]["viewpoint"]);
        $this->assertArrayHasKey("description", $results2["data"]["takeAction"]["viewpoint"]);
        $this->assertArrayHasKey("template", $results2["data"]["takeAction"]["viewpoint"]);
        $this->assertArrayHasKey("actionGroups", $results2["data"]["takeAction"]["viewpoint"]);
        $this->assertGreaterThan(0, count($results2["data"]["takeAction"]["viewpoint"]["actionGroups"]));
        $this->assertGreaterThan(0, count($results2["data"]["takeAction"]["viewpoint"]["actionGroups"][0]["actions"]));
    }

    public function testTakeActionMutationWhileNotAuthorized()
    {
        $takeActionId = $this->getCurrentViewpointActionId();

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
        "characterId": "1",
        "actionId": "$takeActionId"
    }
}
JSON;

        $jsonExpected = <<<JSON
{
    "data": {
        "takeAction":null
    },
    "errors": [{
        "message": "Access denied.",
        "locations": [{"line":2,"column":3}],
        "path":["takeAction"],
        "category": "user"
    }]
}
JSON;

        $this->assertQuery($mutation, $jsonExpected, $mutationVariables);
    }

    public function testTakeActionMutationWhileAuthenticatedButNotAuthorized()
    {
        $takeActionId = $this->getCurrentViewpointActionId(2);

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
        "characterId": "2",
        "actionId": "$takeActionId"
    }
}
JSON;

        $jsonExpected = <<<JSON
{
    "data": {
        "takeAction":null
    },
    "errors": [{
        "message": "Access denied.",
        "locations": [{"line":2,"column":3}],
        "path":["takeAction"],
        "category": "user"
    }]
}
JSON;

        $this->assertQueryAuthorized("c4fEAJLQlaV/47UZl52nAQ==", $mutation, $jsonExpected, $mutationVariables);
    }

    public function testTakeActionMutationIfUserDoesNotOwnCharacterButIsSuperuser()
    {
        $takeActionId = $this->getCurrentViewpointActionId();

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
        "characterId": "1",
        "actionId": "$takeActionId"
    }
}
JSON;

        // We cannot assert the whole query since actionId is randomly generated.
        $results2 = $this->getQueryResultsAuthorized("apiKeyForUser3", $mutation, $mutationVariables);
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

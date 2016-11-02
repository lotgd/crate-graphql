<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests\Functional\GraphQL;

/**
 * Tests getting data from a viewpoint query.
 */
class ViewpointQueryTest extends GraphQLTestCase
{
    public function testViewpointBasicQuery()
    {
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

        $results = $this->getQueryResults($query, $jsonVariables);
        $this->assertArrayHasKey("viewpoint", $results["data"]);
        $this->assertArrayHasKey("title", $results["data"]["viewpoint"]);
        $this->assertArrayHasKey("description", $results["data"]["viewpoint"]);
        $this->assertArrayHasKey("template", $results["data"]["viewpoint"]);
        $this->assertArrayHasKey("actionGroups", $results["data"]["viewpoint"]);
        $this->assertGreaterThan(0, count($results["data"]["viewpoint"]["actionGroups"]));
        $this->assertGreaterThan(0, count($results["data"]["viewpoint"]["actionGroups"][0]["actions"]));
    }
}

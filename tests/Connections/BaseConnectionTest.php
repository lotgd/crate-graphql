<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Tests\Resolver;

use Doctrine\Common\Collections\Collection;

use LotGD\Crate\GraphQL\Tests\WebTestCase;
use LotGD\Crate\GraphQL\AppBundle\GraphQL\Connections\BaseConnection;

class BaseConnectionTest extends WebTestCase
{
    protected function getConnectionImplementation($argumentArray, $data)
    {
        $collection = $this->getMockedCollection();
        $args = $this->getMockedArgument($argumentArray);
        $implementation = new class($collection, $args, $data) extends BaseConnection {
            public function __construct($collection, $args, $data) {
                $this->setConnectionParameters($collection, $args, $data);
            }
        };

        return $implementation;
    }

    protected function getMockedCollection()
    {
        $collection = $this->createMock(Collection::class);
        $collection->method("count")->willReturn(30);
        return $collection;
    }

    public function testIfBaseConnectionWithoutAnyArgumentsProvideMetaDataForFullList()
    {
        $data = ["test" => "testIfBaseConnectionWithoutAnyArgumentsProvideMetaDataForFullList"];
        $connection = $this->getConnectionImplementation([], $data);

        $i = 0;
        foreach ($connection->getEdges() as $edge) {
            $this->assertSame(BaseConnection::encodeCursor($i), $edge["cursor"]);
            $this->assertSame($data, $edge["__data"]);
            $i++;
        }

        $this->assertSame(30, $i);

        $pageInfo = $connection->getPageInfo();
        $this->assertFalse($pageInfo["hasNextPage"]);
        $this->assertFalse($pageInfo["hasPreviousPage"]);
        $this->assertSame(BaseConnection::encodeCursor(0), $pageInfo["startCursor"]);
        $this->assertSame(BaseConnection::encodeCursor(29), $pageInfo["endCursor"]);
    }

    public function testIfBaseConnectionForwardPaginationWorksAsExpected()
    {
        $data = ["test" => "testIfBaseConnectionWithoutAnyArgumentsProvideMetaDataForFullList"];

        // This for-loop runs through the 30 "entries" in pages of 5.
        // In the first run, the offset must be 0 and 4, the second it's 5 and 9, until it reached 25 and 29.
        for ($y = 0; $y < 30; $y+=5) {
            if ($y === 0) {
                $connection = $this->getConnectionImplementation(["first" => 5], $data);
            } else {
                $connection = $this->getConnectionImplementation(["first" => 5, "after" => BaseConnection::encodeCursor($y-1)], $data);
            }

            $i = 0;
            foreach ($connection->getEdges() as $edge) {
                $i++;
            }
            $this->assertSame(5, $i);

            $pageInfo = $connection->getPageInfo();
            if ($y === 0) {
                $this->assertTrue($pageInfo["hasNextPage"]);
                $this->assertFalse($pageInfo["hasPreviousPage"]);
            } elseif ($y === 25) {
                $this->assertFalse($pageInfo["hasNextPage"]);
                $this->assertTrue($pageInfo["hasPreviousPage"]);
            } else {
                $this->assertTrue($pageInfo["hasNextPage"]);
                $this->assertTrue($pageInfo["hasPreviousPage"]);
            }
            $this->assertSame(BaseConnection::encodeCursor($y), $pageInfo["startCursor"]);
            $this->assertSame(BaseConnection::encodeCursor($y+4), $pageInfo["endCursor"]);
        }
    }

    public function testIfBaseConnectionBackwardPaginationWorksAsExpected()
    {
        $data = ["test" => "testIfBaseConnectionWithoutAnyArgumentsProvideMetaDataForFullList"];

        // This for-loop runs through the 30 "entries" in pages of 5.
        // In the first run, the offset must be 29 and 25, the second 24 and 20, until it reached 4 and 0.
        for ($y = 29; $y >= 0; $y-=5) {
            if ($y === 29) {
                $connection = $this->getConnectionImplementation(["last" => 5], $data);
            } else {
                $connection = $this->getConnectionImplementation(["last" => 5, "before" => BaseConnection::encodeCursor($y+1)], $data);
            }

            $i = 0;
            foreach ($connection->getEdges() as $edge) {
                $i++;
            }
            $this->assertSame(5, $i);

            $pageInfo = $connection->getPageInfo();
            if ($y === 4) {
                $this->assertTrue($pageInfo["hasNextPage"]);
                $this->assertFalse($pageInfo["hasPreviousPage"]);
            } elseif ($y === 29) {
                $this->assertFalse($pageInfo["hasNextPage"]);
                $this->assertTrue($pageInfo["hasPreviousPage"]);
            } else {
                $this->assertTrue($pageInfo["hasNextPage"]);
                $this->assertTrue($pageInfo["hasPreviousPage"]);
            }
            $this->assertSame(BaseConnection::encodeCursor($y-4), $pageInfo["startCursor"]);
            $this->assertSame(BaseConnection::encodeCursor($y), $pageInfo["endCursor"]);
        }
    }
}
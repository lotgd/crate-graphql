<?php

namespace LotGD\Crate\GraphQL\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;
use Overblog\GraphQLBundle\Definition\Argument;
use Symfony\Component\Yaml\Yaml;

class WebTestCase extends BaseWebTestCase
{
    static $em;

    public function testNothing() {
        $this->assertTrue(true);
    }

    public function setUp()
    {
        if (static::$em === null) {
            static::$kernel = static::createKernel();
            static::$kernel->boot();

            static::$em = static::$kernel->getContainer()->get("lotgd.core.game")->getEntityManager();
        }

        // get pdo connection
        $pdo = static::$em->getConnection();

        // empty tables
        $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type = 'table'")->fetchAll();

        // get fixture
        $fixture = Yaml::parse(file_get_contents("tests/fixture.yml"));

        foreach ($tables as $table) {
            $tablename = $table["name"];

            $pdo->query("DELETE FROM '{$tablename}'");
            if (isset($fixture[$tablename])) {
                foreach ($fixture[$tablename] as $row) {
                    $fields = implode(",", array_keys($row));
                    $fieldVariables = implode(
                        ",",
                        array_map(function($v) { return ":".$v; }, array_keys($row))
                    );

                    $query = "INSERT INTO '{$tablename}' ($fields) VALUES ($fieldVariables);";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute($row);
                }
            }
        }

        static::$em->clear();
    }

    protected function tearDown()
    {
    }

    protected function startupService($service, $gameMock = null, $containerMock = null)
    {
        if ($containerMock !== null) {
            $service->setContainer($containerMock);
        } else {
            $service->setContainer(static::$kernel->getContainer());
        }

        if ($gameMock !== null) {
            $service->setCoreGameService($gameMock);
        } else {
            $service->setCoreGameService(static::$kernel->getContainer()->get("lotgd.core.game"));
        }
    }

    protected function getMockedArgument(array $arguments): Argument
    {
        $args = $this->createMock(Argument::class);
        $args->method("offsetGet")->will($this->returnCallback(
            function ($key) use ($arguments) {
                return $arguments[$key];
            }
        ));
        $args->method("offsetExists")->will($this->returnCallback(
            function ($key) use ($arguments) {
                return isset($arguments[$key]);
            }
        ));

        return $args;
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
        $files = $requestData["files"] ?? [];
        $server = $requestData["server"] ?? [];
        $content = $requestData["content"] ?? null;

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
}

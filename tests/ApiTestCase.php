<?php

declare(strict_types=1);

namespace App\Tests;

use Faker\Factory;
use Faker\Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Class ApiTestCase
 *
 * @todo: Change implementation to traits
 * @todo: Add abstract REST methods
 *
 * @author Ruslan Valis <ruslan.valis@itomy.ch>
 */
abstract class ApiTestCase extends TestCase
{
    private const CLIENT_URL = "http://localhost";
    private const IS_EXCEPTIONS_ALLOWED = false;

    /**
     * @var Generator
     */
    protected $faker;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $requestData;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var array
     */
    protected $responseData;

    /**
     * @param string $uri
     * @param array $requestData
     */
    protected function executeRemove(string $uri, array $requestData): void {
        $this->executeRequest("delete", $uri, $requestData);
    }

    /**
     * @param array $expectedKeys
     */
    protected function assertRemove(array $expectedKeys = []): void {
        if (null === $this->response || null === $this->responseData) {
            throw new RuntimeException("Request should be executed first");
        }
        self::assertEquals(HttpResponse::HTTP_OK, $this->response->getStatusCode());
        self::assertIsArray($this->responseData);
        self::assertEquals((count($this->responseData) === 0), true);
    }

    /**
     * @param string $uri
     * @param array $requestData
     */
    protected function executeUpdate(string $uri, array $requestData): void {
        $this->executeRequest("patch", $uri, $requestData);
    }

    /**
     * @param array $expectedKeys
     */
    protected function assertUpdate(array $expectedKeys = []): void {
        if (null === $this->response || null === $this->responseData) {
            throw new RuntimeException("Request should be executed first");
        }
        self::assertEquals(HttpResponse::HTTP_OK, $this->response->getStatusCode());

        // Check for expected keys exists
        $requestKeys = array_keys($this->requestData);
        $expectedKeys = array_merge($requestKeys, ["id"], $expectedKeys);
        foreach ($expectedKeys as $expectedKey) {
            self::assertArrayHasKey($expectedKey, $this->responseData);
        }
    }

    /**
     * @param string $uri
     * @param array $requestData
     */
    protected function executeEdit(string $uri, array $requestData): void {
        $this->executeRequest("put", $uri, $requestData);
    }

    /**
     * @param array $expectedKeys
     */
    protected function assertEdit(array $expectedKeys = []): void {
        if (null === $this->response || null === $this->responseData) {
            throw new RuntimeException("Request should be executed first");
        }
        self::assertEquals(HttpResponse::HTTP_OK, $this->response->getStatusCode());

        // Check for expected keys exists
        $requestKeys = array_keys($this->requestData);
        $expectedKeys = array_merge($requestKeys, ["id"], $expectedKeys);
        foreach ($expectedKeys as $expectedKey) {
            self::assertArrayHasKey($expectedKey, $this->responseData);
        }
    }

    /**
     * @param string $uri
     * @param array $requestData
     */
    protected function executeGetAll(string $uri, array $requestData): void {
        $this->executeRequest("get", $uri, $requestData);
    }


    protected function assertGetAll(): void {
        if (null === $this->response || null === $this->responseData) {
            throw new RuntimeException("Request should be executed first");
        }
        self::assertEquals(HttpResponse::HTTP_OK, $this->response->getStatusCode());
        self::assertIsArray($this->responseData);
    }

    /**
     * @param string $uri
     * @param array $requestData
     */
    protected function executeGetOne(string $uri, array $requestData): void {
        $this->executeRequest("get", $uri, $requestData);
    }

    /**
     * @param array $expectedKeys
     */
    protected function assertGetOne(array $expectedKeys = []): void {
        if (null === $this->response || null === $this->responseData) {
            throw new RuntimeException("Request should be executed first");
        }
        self::assertEquals(HttpResponse::HTTP_OK, $this->response->getStatusCode());

        // Check for expected keys exists
        $requestKeys = array_keys($this->requestData);
        $expectedKeys = array_merge($requestKeys, ["id"], $expectedKeys);
        foreach ($expectedKeys as $expectedKey) {
            self::assertArrayHasKey($expectedKey, $this->responseData);
        }
    }

    /**
     * @param string $uri
     * @param array $requestData
     */
    protected function executePost(string $uri, array $requestData): void {
        $this->executeRequest("post", $uri, $requestData);
    }

    /**
     * @param array $expectedKeys
     */
    protected function assertPost(array $expectedKeys = []): void {
        if (null === $this->response || null === $this->responseData) {
            throw new RuntimeException("Request should be executed first");
        }
        self::assertEquals(HttpResponse::HTTP_CREATED, $this->response->getStatusCode());

        // Check for expected keys exists
        $requestKeys = array_keys($this->requestData);
        $expectedKeys = array_merge($requestKeys, ["id"], $expectedKeys);
        foreach ($expectedKeys as $expectedKey) {
            self::assertArrayHasKey($expectedKey, $this->responseData);
        }
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $requestData
     */
    private function executeRequest(string $method, string $uri, array $requestData): void
    {
        $this->requestData = $requestData;
        $encodedData = json_encode($this->requestData);
        $convertedUri = self::CLIENT_URL . $uri;

        try {
            $this->response = $this->client->request($method, $convertedUri, ['body' => $encodedData]);
        } catch(GuzzleException $exception) {
            $methodUpper = strtoupper($method);
            throw new RuntimeException("[{$methodUpper}] Request execution error: " . PHP_EOL
                                       . "[Request] URI: {$convertedUri}" . PHP_EOL
                                       . "[Request] Data: " . $encodedData . PHP_EOL
                                       . "[Error] Code: " . $exception->getCode() . PHP_EOL
                                       . "[Error] Message: " . $exception->getMessage() . PHP_EOL
                                       . "[Error] Stack trace: " . PHP_EOL . $exception->getTraceAsString());
        }

        $this->responseData = json_decode((string)$this->response->getBody(), true);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // TODO: move part of code to setUpClass method
        // TODO: add database purge / application mocking

        $this->faker = Factory::create();
        $this->client = new Client([
            'base_url' => self::CLIENT_URL,
            'defaults' => [
                'exceptions' => self::IS_EXCEPTIONS_ALLOWED
            ]
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->requestData = null;
        $this->response = null;
        $this->responseData = null;
    }
}

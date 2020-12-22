<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;
use Faker\Factory;
use GuzzleHttp\Client;

/**
 * Class TodoListItemControllerTest
 *
 * @covers \App\Controller\TodoListItemController
 *
 * @package App\Tests\Controller
 * @author Ruslan Valis <ruslan.valis@itomy.ch>
 */
class TodoListItemControllerTest extends ApiTestCase
{

    /**
     * @var int
     */
    private static $createdParentEntryId;

    /**
     * @var int
     */
    private static $createdEntryId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // TODO: remove after implementing staticClient and staticFaker in ApiTestCase class
        $faker = Factory::create();
        $client = new Client([
                                'base_url' => self::CLIENT_URL,
                                'defaults' => [
                                    'exceptions' => self::IS_EXCEPTIONS_ALLOWED
                                ]
                            ]);

        // TODO: use executePost of parent when will have static method too
        $encodedData = json_encode([
                                       "name" => $faker->sentence,
                                       "description" => $faker->text
                                   ]);
        $response = $client->post(self::CLIENT_URL . "/list", ['body' => $encodedData]);
        $responseData = json_decode((string)$response->getBody(), true);
        self::$createdParentEntryId = $responseData["id"];
    }

    public function test_new(): void
    {
        // Execute request
        $this->executePost(
            "/list/" . self::$createdParentEntryId . "/item",
            [
                "name" => $this->faker->sentence,
                "description" => $this->faker->text,
                "is_checked" => false
            ]
        );

        // Assert response
        $this->assertPost();

        self::$createdEntryId = $this->responseData["id"];
    }

    public function test_show(): void
    {
        // Execute request
        $this->executeGetOne(
            "/list/" . self::$createdParentEntryId . "/item/" . self::$createdEntryId,
            []
        );

        // Assert response
        $this->assertGetOne();
    }

    public function test_index(): void
    {
        // Execute request
        $this->executeGetAll(
            "/list/" . self::$createdParentEntryId . "/item",
            []
        );

        // Assert response
        $this->assertGetAll();
    }

    public function test_edit(): void
    {
        // Execute request
        $this->executeEdit(
            "/list/" . self::$createdParentEntryId . "/item/" . self::$createdEntryId,
            [
                "name" => $this->faker->sentence,
                "description" => ""
            ]
        );

        // Assert response
        $this->assertEdit();
    }

    public function test_update(): void
    {
        // Execute request
        $this->executeUpdate(
            "/list/" . self::$createdParentEntryId . "/item/" . self::$createdEntryId,
            [
                "name" => $this->faker->sentence,
                "description" => $this->faker->text,
                "is_checked" => true
            ]
        );

        // Assert response
        $this->assertUpdate();
    }

    public function test_remove(): void
    {
        // Execute request
        $this->executeRemove(
            "/list/" . self::$createdParentEntryId . "/item/" . self::$createdEntryId,
            []
        );

        // Assert response
        $this->assertRemove();
    }

}

<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;

/**
 * Class TodoListControllerTest
 *
 * @covers \App\Controller\TodoListController
 *
 * @package App\Tests\Controller
 * @author Ruslan Valis <ruslan.valis@itomy.ch>
 */
class TodoListControllerTest extends ApiTestCase
{

    /**
     * @var int
     */
    private static $createdEntryId;

    public function test_new(): void
    {
        // Execute request
        $this->executePost(
            "/list",
            [
                "name" => $this->faker->sentence,
                "description" => $this->faker->text
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
            "/list/" . self::$createdEntryId,
            []
        );

        // Assert response
        $this->assertGetOne();
    }

    public function test_index(): void
    {
        // Execute request
        $this->executeGetAll(
            "/list",
            []
        );

        // Assert response
        $this->assertGetAll();
    }

    public function test_edit(): void
    {
        // Execute request
        $this->executeEdit(
            "/list/" . self::$createdEntryId,
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
            "/list/" . self::$createdEntryId,
            [
                "name" => $this->faker->sentence,
                "description" => $this->faker->text
            ]
        );

        // Assert response
        $this->assertUpdate();
    }

    public function test_remove(): void
    {
        // Execute request
        $this->executeRemove(
            "/list/" . self::$createdEntryId,
            []
        );

        // Assert response
        $this->assertRemove();
    }

}

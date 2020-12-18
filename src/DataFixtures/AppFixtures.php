<?php

namespace App\DataFixtures;

use App\Entity\TodoList;
use App\Entity\TodoListItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private const LISTS_COUNT_MIN = 1;
    private const LISTS_COUNT_MAX = 5;

    private const LIST_ITEMS_MIN = 0;
    private const LIST_ITEMS_MAX = 20;

    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var ObjectManager
     */
    private $manager;

    public function load(ObjectManager $manager): void
    {
        // remember class-required entities
        $this->faker = Factory::create();
        $this->manager = $manager;

        // create multiple lists
        $listsCount = $this->faker->numberBetween(self::LISTS_COUNT_MIN, self::LISTS_COUNT_MAX);
        for ($listIndex = 0; $listIndex < $listsCount; $listIndex++) {
            // create single list
            $todoList = $this->_createTodoList();

            // create multiple list items
            $listItemsCount = $this->faker->numberBetween(self::LIST_ITEMS_MIN, self::LIST_ITEMS_MAX);
            for ($listItemIndex = 0; $listItemIndex < $listItemsCount; $listItemIndex++) {
                $this->_createTodoListItem($todoList);
            }
        }

        // write changes to database
        $this->manager->flush();
    }

    /**
     * Create instance of TodoList entity with internal TodoListItem entities
     * @return TodoList
     */
    private function _createTodoList(): TodoList {
        $todoListItem = new TodoList();
        $todoListItem->setName($this->faker->sentence);
        $todoListItem->setDescription($this->faker->text);
        $todoListItem->setCreatedAt($this->faker->dateTime());
        $todoListItem->setUpdatedAt(null);

        $this->manager->persist($todoListItem);
        return $todoListItem;
    }

    /**
     * Create instance of TodoListItem, related to the $list instance
     * @param TodoList $list
     * @return TodoListItem
     */
    private function _createTodoListItem(TodoList $list): TodoListItem {
        $todoListItem = new TodoListItem();
        $todoListItem->setName($this->faker->sentence);
        $todoListItem->setList($list);
        $todoListItem->setDescription($this->faker->text);
        $todoListItem->setCreatedAt($this->faker->dateTime());
        $todoListItem->setUpdatedAt(null);
        $todoListItem->setIsChecked($this->faker->boolean);

        $this->manager->persist($todoListItem);
        return $todoListItem;
    }
}

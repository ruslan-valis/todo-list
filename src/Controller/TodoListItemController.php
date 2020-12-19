<?php

declare(strict_types=1);

namespace App\Controller;


use App\Repository\TodoListItemRepository;
use App\Repository\TodoListRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TodoListItemController
 *
 * @package App\Controller
 * @author Ruslan Valis <ruslan.valis@itomy.ch>
 *
 * @Route("/list/{listId}/item")
 */
class TodoListItemController
{
    /**
     * @var TodoListRepository
     */
    private $todoListRepository;

    /**
     * @var TodoListItemRepository
     */
    private $todoListItemRepository;

    public function __construct(TodoListRepository $todoListRepository, TodoListItemRepository $todoListItemRepository)
    {
        $this->todoListRepository = $todoListRepository;
        $this->todoListItemRepository = $todoListItemRepository;
    }

    /**
     * @Route("", name="new_list_item", methods={"POST"})
     * @param int $listId
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function new(int $listId, Request $request): JsonResponse
    {
        // Get parent list
        $todoList = $this->todoListRepository->findOneById($listId);
        if (!$todoList) {
            throw new NotFoundHttpException("Undefined list with id {$listId}");
        }

        // Parse parameters
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $description = $data['description'];
        $is_checked = $data['is_checked'];

        // Check parameters
        // TODO: Update with Entity Asserts and Validator
        if (empty($name)) {
            throw new NotFoundHttpException("Missing required parameters");
        }

        // Process action
        $todoListItem = $this->todoListItemRepository->create($todoList, $name, $description, $is_checked);

        // Return response
        return new JsonResponse($todoListItem->toArray(), Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="get_one_list_item", methods={"GET"})
     * @param int $listId
     * @param int $id
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function show(int $listId, int $id): JsonResponse
    {
        // Get parent list
        $todoList = $this->todoListRepository->findOneById($listId);
        if (!$todoList) {
            throw new NotFoundHttpException("Undefined list with id {$listId}");
        }

        // Get requested entity
        $todoListItem = $this->todoListItemRepository->findOneById($id);
        if (!$todoListItem) {
            throw new NotFoundHttpException("Undefined list item with id {$id}");
        }

        // Check parent list match
        if ($todoList->getId() !== $todoListItem->getList()->getId()) {
            throw new NotFoundHttpException("List item with id ${id} is not match list with id ${listId}");
        }

        // Return response
        return new JsonResponse($todoListItem->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("", name="get_all_list_items", methods={"GET"})
     * @param int $listId
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function index(int $listId): JsonResponse
    {
        // Get parent list
        $todoList = $this->todoListRepository->findOneById($listId);
        if (!$todoList) {
            throw new NotFoundHttpException("Undefined list with id {$listId}");
        }

        // Execute action
        $renderedTodoListItems = [];
        foreach ($todoList->getItems() as $todoListItem) {
            $renderedTodoListItems[] = $todoListItem->toArray();
        }

        // Return response
        return new JsonResponse($renderedTodoListItems, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="edit_list_item", methods={"PUT"})
     * @param int $listId
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function edit(int $listId, int $id, Request $request): JsonResponse
    {
        // Get parent list
        $todoList = $this->todoListRepository->findOneById($listId);
        if (!$todoList) {
            throw new NotFoundHttpException("Undefined list with id {$listId}");
        }

        // Get requested entity
        $todoListItem = $this->todoListItemRepository->findOneById($id);
        if (!$todoListItem) {
            throw new NotFoundHttpException("Undefined list item with id {$id}");
        }

        // Check parent list match
        if ($todoList->getId() !== $todoListItem->getList()->getId()) {
            throw new NotFoundHttpException("List item with id ${id} is not match list with id ${listId}");
        }

        // Parse parameters
        $data = json_decode($request->getContent(), true);

        // Update only changed parameters
        empty($data['name']) ? true : $todoListItem->setName($data['name']);
        array_key_exists("description", $data) ? $todoListItem->setDescription($data['description']) : true;
        array_key_exists("is_checked", $data) ? $todoListItem->setIsChecked($data['is_checked']) : true;

        // Execute action
        $updatedTodoListItem = $this->todoListItemRepository->update($todoListItem);

        // Return response
        return new JsonResponse($updatedTodoListItem->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="update_list_item", methods={"PATCH"})
     * @param int $listId
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function update(int $listId, int $id, Request $request): JsonResponse
    {
        // Get parent list
        $todoList = $this->todoListRepository->findOneById($listId);
        if (!$todoList) {
            throw new NotFoundHttpException("Undefined list with id {$listId}");
        }

        // Get requested entity
        $todoListItem = $this->todoListItemRepository->findOneById($id);
        if (!$todoListItem) {
            throw new NotFoundHttpException("Undefined list item with id {$id}");
        }

        // Check parent list match
        if ($todoList->getId() !== $todoListItem->getList()->getId()) {
            throw new NotFoundHttpException("List item with id ${id} is not match list with id ${listId}");
        }

        // Parse parameters
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $description = $data['description'];
        $is_checked = $data['is_checked'];

        // Check parameters
        // TODO: Update with Entity Asserts and Validator
        if (empty($name)) {
            throw new NotFoundHttpException("Missing required parameters");
        }

        // Update all parameters
        $todoListItem->setName($name);
        $todoListItem->setDescription($description);
        $todoListItem->setIsChecked($is_checked);

        // Execute action
        $updatedTodoListItem = $this->todoListItemRepository->update($todoListItem);

        // Return response
        return new JsonResponse($updatedTodoListItem->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="remove_list_item", methods={"DELETE"})
     * @param int $listId
     * @param int $id
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function remove(int $listId, int $id): JsonResponse
    {
        // Get parent list
        $todoList = $this->todoListRepository->findOneById($listId);
        if (!$todoList) {
            throw new NotFoundHttpException("Undefined list with id {$listId}");
        }

        // Get requested entity
        $todoListItem = $this->todoListItemRepository->findOneById($id);
        if (!$todoListItem) {
            throw new NotFoundHttpException("Undefined list item with id {$id}");
        }

        // Check parent list match
        if ($todoList->getId() !== $todoListItem->getList()->getId()) {
            throw new NotFoundHttpException("List item with id ${id} is not match list with id ${listId}");
        }

        // Execute action
        $this->todoListItemRepository->remove($todoListItem);

        // Return response
        return new JsonResponse([], Response::HTTP_OK);
    }

}

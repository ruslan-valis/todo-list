<?php

declare(strict_types=1);

namespace App\Controller;


use App\Repository\TodoListRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TodoListController
 *
 * @package App\Controller
 * @author Ruslan Valis <ruslan.valis@itomy.ch>
 *
 * @Route("/list")
 */
class TodoListController
{
    /**
     * @var TodoListRepository
     */
    private $todoListRepository;

    public function __construct(TodoListRepository $todoListItemRepository)
    {
        $this->todoListRepository = $todoListItemRepository;
    }

    /**
     * @Route("", name="new_list", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function new(Request $request): JsonResponse
    {
        // Parse parameters
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $description = $data['description'];

        // Check parameters
        // TODO: Update with Entity Asserts and Validator
        if (empty($name)) {
            throw new NotFoundHttpException("Missing required parameters");
        }

        // Process action
        $todoList = $this->todoListRepository->create($name, $description);

        // Return response
        return new JsonResponse($todoList->toArray(), Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="get_one_list", methods={"GET"})
     * @param int $id
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function show(int $id): JsonResponse
    {
        // Get requested entity
        $todoList = $this->todoListRepository->findOneById($id);
        if (!$todoList) {
            throw new NotFoundHttpException("Undefined list with id {$id}");
        }

        // Return response
        return new JsonResponse($todoList->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("", name="get_all_lists", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        // Get requested entities
        $todoLists = $this->todoListRepository->findAll();

        // Execute action
        $renderedTodoLists = [];
        foreach ($todoLists as $todoList) {
            $renderedTodoLists[] = $todoList->toArray();
        }

        // Return response
        return new JsonResponse($renderedTodoLists, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="edit_list", methods={"PUT"})
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function edit(int $id, Request $request): JsonResponse
    {
        // Get requested entity
        $todoList = $this->todoListRepository->findOneById($id);
        if (!$todoList) {
            throw new NotFoundHttpException("Undefined list with id {$id}");
        }

        // Parse parameters
        $data = json_decode($request->getContent(), true);

        // Update only changed parameters
        empty($data['name']) ? true : $todoList->setName($data['name']);
        array_key_exists("description", $data) ? $todoList->setDescription($data['description']) : true;

        // Execute action
        $updatedTodoList = $this->todoListRepository->update($todoList);

        // Return response
        return new JsonResponse($updatedTodoList->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="update_list", methods={"PATCH"})
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function update(int $id, Request $request): JsonResponse
    {
        // Get requested entity
        $todoList = $this->todoListRepository->findOneById($id);
        if (!$todoList) {
            throw new NotFoundHttpException("Undefined list with id {$id}");
        }

        // Parse parameters
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $description = $data['description'];

        // Check parameters
        // TODO: Update with Entity Asserts and Validator
        if (empty($name)) {
            throw new NotFoundHttpException("Missing required parameters");
        }

        // Update all parameters
        $todoList->setName($name);
        $todoList->setDescription($description);

        // Execute action
        $updatedTodoList = $this->todoListRepository->update($todoList);

        // Return response
        return new JsonResponse($updatedTodoList->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="remove_list", methods={"DELETE"})
     * @param int $id
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function remove(int $id): JsonResponse
    {
        // Get requested entity
        $todoList = $this->todoListRepository->findOneById($id);
        if (!$todoList) {
            throw new NotFoundHttpException("Undefined list with id {$id}");
        }

        // Execute action
        $this->todoListRepository->remove($todoList);

        // Return response
        return new JsonResponse([], Response::HTTP_OK);
    }

}

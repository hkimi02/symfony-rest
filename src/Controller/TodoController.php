<?php

namespace App\Controller;

use App\Entity\Todo;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]

class TodoController extends AbstractController
{
    #[Route('/todos', name: 'app_todo', methods: ['post'])]
    public function create(Request $request, ManagerRegistry $managerRegistry): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $todo = new Todo();
        $todo->setName($data['name']);
        $todo->setDescription($data['description']);

        $managerRegistry->getManager()->persist($todo);
        $managerRegistry->getManager()->flush();

        return new JsonResponse(['status' => 'Todo created!'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/todos', name: 'app_todos', methods: ['get'])]
    public function list(ManagerRegistry $managerRegistry): JsonResponse
    {
        $todos = $managerRegistry->getRepository(Todo::class)->findAll();

        $data = [];

        foreach ($todos as $todo) {
            $data[] = [
                'id' => $todo->getId(),
                'name' => $todo->getName(),
                'description' => $todo->getDescription(),
            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/todos/{id}', name: 'app_todo_show', methods: ['get'])]
    public function show(int $id, ManagerRegistry $managerRegistry): JsonResponse
    {
        $todo = $managerRegistry->getRepository(Todo::class)->find($id);

        if (!$todo) {
            return new JsonResponse(['status' => 'Todo not found!'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $todo->getId(),
            'name' => $todo->getName(),
            'description' => $todo->getDescription(),
        ];

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/todos/{id}', name: 'app_todo_update', methods: ['put'])]
    public function update(int $id, Request $request, ManagerRegistry $managerRegistry): JsonResponse
    {
        $todo = $managerRegistry->getRepository(Todo::class)->find($id);

        if (!$todo) {
            return new JsonResponse(['status' => 'Todo not found!'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $todo->setName($data['name']);
        $todo->setDescription($data['description']);

        $managerRegistry->getManager()->persist($todo);
        $managerRegistry->getManager()->flush();

        return new JsonResponse(['status' => 'Todo updated!'], JsonResponse::HTTP_OK);
    }

    #[Route('/todos/{id}', name: 'app_todo_delete', methods: ['delete'])]
    public function delete(int $id, ManagerRegistry $managerRegistry): JsonResponse
    {
        $todo = $managerRegistry->getRepository(Todo::class)->find($id);

        if (!$todo) {
            return new JsonResponse(['status' => 'Todo not found!'], JsonResponse::HTTP_NOT_FOUND);
        }

        $managerRegistry->getManager()->remove($todo);
        $managerRegistry->getManager()->flush();

        return new JsonResponse(['status' => 'Todo deleted!'], JsonResponse::HTTP_OK);
    }
}

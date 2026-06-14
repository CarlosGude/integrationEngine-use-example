<?php

declare(strict_types=1);

namespace App\Traditional\Controller;

use App\Traditional\DummyRestApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CreateEmployeeController extends AbstractController
{
    public function __construct(private readonly DummyRestApiClient $client) {}

    #[Route('/traditional/employees', name: 'traditional_employee_create', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->toArray();

        $e = $this->client->createEmployee(
            name:   $data['name'],
            salary: (int) $data['salary'],
            age:    (int) $data['age'],
        );

        return new JsonResponse([
            'id'     => (int) $e['id'],
            'name'   => $e['name'],
            'salary' => (int) $e['salary'],
            'age'    => (int) $e['age'],
        ], 201);
    }
}

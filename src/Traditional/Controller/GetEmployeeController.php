<?php

declare(strict_types=1);

namespace App\Traditional\Controller;

use App\Traditional\DummyRestApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class GetEmployeeController extends AbstractController
{
    public function __construct(private readonly DummyRestApiClient $client) {}

    #[Route('/traditional/employees/{id}', name: 'traditional_employee_get', methods: ['GET'])]
    public function __invoke(int $id): JsonResponse
    {
        $e = $this->client->getEmployee($id);

        return new JsonResponse([
            'id'     => (int) $e['id'],
            'name'   => $e['employee_name'],
            'salary' => (int) $e['employee_salary'],
            'age'    => (int) $e['employee_age'],
        ]);
    }
}

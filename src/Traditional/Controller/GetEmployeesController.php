<?php

declare(strict_types=1);

namespace App\Traditional\Controller;

use App\Traditional\DummyRestApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class GetEmployeesController extends AbstractController
{
    public function __construct(private readonly DummyRestApiClient $client) {}

    #[Route('/traditional/employees', name: 'traditional_employees_list', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $data = $this->client->getEmployees();

        // Field names must be known here — the controller is coupled to the API schema.
        return new JsonResponse(array_map(fn(array $e) => [
            'id'     => (int) $e['id'],
            'name'   => $e['employee_name'],
            'salary' => (int) $e['employee_salary'],
            'age'    => (int) $e['employee_age'],
        ], $data));
    }
}

<?php

declare(strict_types=1);

namespace App\Traditional\Controller;

use App\Traditional\DummyRestApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class GetManyEmployeesController extends AbstractController
{
    public function __construct(private readonly DummyRestApiClient $client) {}

    /**
     * Sequential batch — total time grows linearly with the number of IDs.
     * Usage: GET /traditional/employees/batch?ids=1,2,3
     */
    #[Route('/traditional/employees/batch', name: 'traditional_employees_batch', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $ids = array_map('intval', explode(',', $request->query->getString('ids')));

        $data = $this->client->getManyEmployees(...$ids);

        return new JsonResponse(array_map(fn(array $e) => [
            'id'     => (int) $e['id'],
            'name'   => $e['employee_name'],
            'salary' => (int) $e['employee_salary'],
            'age'    => (int) $e['employee_age'],
        ], $data));
    }
}

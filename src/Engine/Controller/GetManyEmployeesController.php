<?php

declare(strict_types=1);

namespace App\Engine\Controller;

use App\Engine\Application\EmployeeService;
use App\Engine\Domain\Employee;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class GetManyEmployeesController extends AbstractController
{
    public function __construct(private readonly EmployeeService $employeeService) {}

    /**
     * Concurrent batch — total time ≈ slowest single request.
     * Usage: GET /engine/employees/batch?ids=1,2,3
     */
    #[Route('/engine/employees/batch', name: 'engine_employees_batch', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $ids = array_map('intval', explode(',', $request->query->getString('ids')));

        return new JsonResponse(
            array_map(fn(Employee $e) => $e->toArray(), $this->employeeService->findMany(...$ids)),
        );
    }
}

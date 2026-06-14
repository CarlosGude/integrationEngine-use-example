<?php

declare(strict_types=1);

namespace App\Engine\Controller;

use App\Engine\Application\EmployeeService;
use App\Engine\Domain\Employee;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class GetEmployeesController extends AbstractController
{
    public function __construct(private readonly EmployeeService $employeeService) {}

    #[Route('/engine/employees', name: 'engine_employees_list', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(
            array_map(fn(Employee $e) => $e->toArray(), $this->employeeService->findAll()),
        );
    }
}

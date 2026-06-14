<?php

declare(strict_types=1);

namespace App\Engine\Controller;

use App\Engine\Application\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class GetEmployeeController extends AbstractController
{
    public function __construct(private readonly EmployeeService $employeeService) {}

    #[Route('/engine/employees/{id}', name: 'engine_employee_get', methods: ['GET'])]
    public function __invoke(int $id): JsonResponse
    {
        return new JsonResponse($this->employeeService->find($id)->toArray());
    }
}

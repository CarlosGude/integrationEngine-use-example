<?php

declare(strict_types=1);

namespace App\Engine\Application\Ports\DummyRestApi;

use App\Engine\Infrastructure\DummyRestApiGateway;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CreateEmployeeController extends AbstractController
{
    public function __construct(private readonly DummyRestApiGateway $gateway) {}

    #[Route('/engine/employees', name: 'engine_employee_create', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->toArray();

        $employee = $this->gateway->create(
            name: $data['name'],
            salary: (int) $data['salary'],
            age: (int) $data['age'],
        );

        return new JsonResponse($employee->toArray(), 201);
    }
}

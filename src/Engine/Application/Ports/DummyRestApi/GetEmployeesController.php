<?php

declare(strict_types=1);

namespace App\Engine\Application\Ports\DummyRestApi;

use App\Engine\Domain\Employee;
use App\Engine\Infrastructure\DummyRestApiGateway;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class GetEmployeesController extends AbstractController
{
    public function __construct(private readonly DummyRestApiGateway $gateway) {}

    #[Route('/engine/employees', name: 'engine_employees_list', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(
            array_map(static fn (Employee $e) => $e->toArray(), $this->gateway->findAll()),
        );
    }
}

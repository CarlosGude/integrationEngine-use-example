<?php

declare(strict_types=1);

namespace App\Engine\Application\Ports\DummyRestApi;

use App\Engine\Infrastructure\DummyRestApiGateway;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class GetEmployeeController extends AbstractController
{
    public function __construct(private readonly DummyRestApiGateway $gateway) {}

    #[Route('/engine/employees/{id}', name: 'engine_employee_get', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function __invoke(int $id): JsonResponse
    {
        return new JsonResponse($this->gateway->find($id)->toArray());
    }
}

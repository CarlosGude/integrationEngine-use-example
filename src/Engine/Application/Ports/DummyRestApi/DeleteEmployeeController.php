<?php

declare(strict_types=1);

namespace App\Engine\Application\Ports\DummyRestApi;

use App\Engine\Infrastructure\DummyRestApiGateway;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class DeleteEmployeeController extends AbstractController
{
    public function __construct(private readonly DummyRestApiGateway $gateway) {}

    #[Route('/engine/employees/{id}', name: 'engine_employee_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function __invoke(int $id): JsonResponse
    {
        $this->gateway->delete($id);

        return new JsonResponse(null, 204);
    }
}

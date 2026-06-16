<?php

declare(strict_types=1);

namespace App\Engine\Application\Ports\DummyRestApi;

use App\Engine\Domain\Employee;
use App\Engine\Infrastructure\DummyRestApiGateway;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class GetManyEmployeesResilientController extends AbstractController
{
    public function __construct(private readonly DummyRestApiGateway $gateway) {}

    /**
     * Tolerant concurrent batch — individual failures are included in the response
     * instead of aborting the whole request. Uses sendMany() + BatchResultCollection.
     *
     * Usage: GET /engine/employees/batch-resilient?ids=1,2,3
     */
    #[Route('/engine/employees/batch-resilient', name: 'engine_employees_batch_resilient', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $ids = array_values(array_filter(
            array_map('intval', explode(',', $request->query->getString('ids'))),
        ));

        if ([] === $ids) {
            return new JsonResponse(['employees' => [], 'errors' => []]);
        }

        $result = $this->gateway->findManyResilient(...$ids);

        return new JsonResponse([
            'employees' => array_map(static fn (Employee $e) => $e->toArray(), $result['employees']),
            'errors' => array_map(static fn (\Throwable $e) => $e->getMessage(), $result['errors']),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\DummyRestApi;

use App\Engine\Infrastructure\Integrations\DummyRestApi\CreateEmployee\Request\CreateEmployeeAction;
use App\Engine\Infrastructure\Integrations\DummyRestApi\CreateEmployee\Request\CreateEmployeeBody;
use App\Engine\Infrastructure\Integrations\DummyRestApi\CreateEmployee\Response\CreateEmployeeResponse;
use App\Engine\Infrastructure\Integrations\DummyRestApi\DeleteEmployee\Request\DeleteEmployeeAction;
use App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployee\Request\GetEmployeeAction;
use App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployee\Response\GetEmployeeResponse;
use App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployees\Request\GetEmployeesAction;
use App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployees\Response\GetEmployeesResponse;
use IntegrationEngine\Core\Batch\BatchResultCollection;
use IntegrationEngine\Core\Batch\EngineRequest;
use IntegrationEngine\Core\Contract\Action\DefaultActionContext;
use IntegrationEngine\Core\IntegrationEngine;
use IntegrationEngine\Core\Registry\IntegrationName;
use IntegrationEngine\Core\Registry\IntegrationRegistry;

class DummyRestApiIntegration implements IntegrationName
{
    public const string NAME = 'dummy_rest_api';

    private IntegrationEngine $engine;

    public function __construct(IntegrationRegistry $registry)
    {
        $this->engine = $registry->get(self::NAME);
    }

    public function getEmployees(): GetEmployeesResponse
    {
        $response = $this->engine->send(GetEmployeesAction::getName());

        \assert($response instanceof GetEmployeesResponse);

        return $response;
    }

    public function getEmployee(int $id): GetEmployeeResponse
    {
        $response = $this->engine->send(
            actionName: GetEmployeeAction::getName(),
            context: DefaultActionContext::create(['id' => $id]),
        );

        \assert($response instanceof GetEmployeeResponse);

        return $response;
    }

    public function createEmployee(string $name, int $salary, int $age): CreateEmployeeResponse
    {
        $response = $this->engine->send(
            actionName: CreateEmployeeAction::getName(),
            body: CreateEmployeeBody::create([
                'name' => $name,
                'salary' => (string) $salary,
                'age' => (string) $age,
            ]),
        );

        \assert($response instanceof CreateEmployeeResponse);

        return $response;
    }

    /**
     * Concurrent batch (fail-fast) — throws on the first failed request.
     * Total time ≈ the slowest single request, not the sum of all.
     *
     * @return array<int, GetEmployeeResponse>
     */
    public function getManyEmployees(int ...$ids): array
    {
        $requests = [];
        foreach ($ids as $id) {
            $requests[$id] = EngineRequest::create(
                actionName: GetEmployeeAction::getName(),
                context: DefaultActionContext::create(['id' => $id]),
            );
        }

        /** @var array<int, GetEmployeeResponse> */
        return $this->engine->sendManyOrFail($requests);
    }

    public function deleteEmployee(int $id): void
    {
        $this->engine->send(
            actionName: DeleteEmployeeAction::getName(),
            context: DefaultActionContext::create(['id' => $id]),
        );
    }

    /**
     * Concurrent batch (tolerant) — individual failures don't abort the rest.
     * Returns a BatchResultCollection with both successes and errors keyed by ID.
     */
    public function getManyEmployeesTolerant(int ...$ids): BatchResultCollection
    {
        $requests = [];
        foreach ($ids as $id) {
            $requests[$id] = EngineRequest::create(
                actionName: GetEmployeeAction::getName(),
                context: DefaultActionContext::create(['id' => $id]),
            );
        }

        return $this->engine->sendMany($requests);
    }
}

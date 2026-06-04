<?php

declare(strict_types=1);

namespace App\Infrastructure\Integrations\DummyRestApi;

use App\Infrastructure\Integrations\DummyRestApi\Dtos\Employee;
use App\Infrastructure\Integrations\DummyRestApi\GetEmployee\Request\GetEmployeeAction;
use App\Infrastructure\Integrations\DummyRestApi\GetEmployee\Request\GetEmployeeRequestContext;
use App\Infrastructure\Integrations\DummyRestApi\GetEmployee\Response\GetEmployeeResponse;
use App\Infrastructure\Integrations\DummyRestApi\GetEmployees\Request\GetEmployeesAction;
use App\Infrastructure\Integrations\DummyRestApi\GetEmployees\Response\GetEmployeesResponse;
use IntegrationEngine\Core\IntegrationEngine;
use IntegrationEngine\Core\Registry\IntegrationName;
use IntegrationEngine\Core\Registry\IntegrationRegistry;

final class DummyRestApiIntegration implements IntegrationName
{
    public const string NAME = 'dummy_rest_api';
    private IntegrationEngine $integrationEngine;

    public function __construct(
        IntegrationRegistry $registry
    )
    {
        $this->integrationEngine = $registry->get(self::NAME);
    }

    public function getEmployees(): GetEmployeesResponse
    {
        $response = $this->integrationEngine->send(GetEmployeesAction::getName());

        \assert($response instanceof GetEmployeesResponse);

        return $response;
    }

    public function getEmployee(int $id): GetEmployeeResponse
    {
        $response = $this->integrationEngine->send(
            actionName: GetEmployeeAction::getName(),
            context: GetEmployeeRequestContext::create(['id' => $id])
        );

        \assert($response instanceof GetEmployeeResponse);

        return $response;
    }
}

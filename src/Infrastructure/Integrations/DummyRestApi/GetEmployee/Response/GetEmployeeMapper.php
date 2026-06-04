<?php

declare(strict_types=1);

namespace App\Infrastructure\Integrations\DummyRestApi\GetEmployee\Response;

use App\Infrastructure\Integrations\DummyRestApi\Dtos\Employee;
use IntegrationEngine\Core\Contract\AbstractAction;
use IntegrationEngine\Core\Contract\AbstractMapper;
use IntegrationEngine\Core\Contract\ResponseInterface;
use App\Infrastructure\Integrations\DummyRestApi\GetEmployee\Request\GetEmployeeAction;

final class GetEmployeeMapper extends AbstractMapper
{
    public static function getAction(): string
    {
        return GetEmployeeAction::class;
    }

    protected static function transform(AbstractAction $action, array $response): ResponseInterface
    {
        $employee = Employee::create(
            id: (int) $response['data']['id'],
            name: $response['data']['employee_name'],
            age: (int) $response['data']['employee_age']
        );

        return new GetEmployeeResponse($employee);
    }
}

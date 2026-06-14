<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployees\Response;

use App\Engine\Infrastructure\Integrations\DummyRestApi\Dtos\Employee;
use App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployees\Request\GetEmployeesAction;
use IntegrationEngine\Core\Contract\Action\AbstractAction;
use IntegrationEngine\Core\Contract\Mapper\AbstractMapper;
use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final class GetEmployeesMapper extends AbstractMapper
{
    public static function getAction(): string
    {
        return GetEmployeesAction::class;
    }

    protected static function transform(AbstractAction $action, array $response): ResponseInterface
    {
        $employees = array_map(
            fn(array $data) => Employee::create(
                id:     (int) $data['id'],
                name:   $data['employee_name'],
                salary: (int) $data['employee_salary'],
                age:    (int) $data['employee_age'],
            ),
            $response['data'],
        );

        return new GetEmployeesResponse($employees);
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Integrations\DummyRestApi\GetEmployees\Response;

use App\Infrastructure\Integrations\DummyRestApi\Collections\EmployeeCollection;
use App\Infrastructure\Integrations\DummyRestApi\Dtos\Employee;
use IntegrationEngine\Core\Contract\AbstractAction;
use IntegrationEngine\Core\Contract\AbstractMapper;
use IntegrationEngine\Core\Contract\ResponseInterface;
use App\Infrastructure\Integrations\DummyRestApi\GetEmployees\Request\GetEmployeesAction;

final class GetEmployeesMapper extends AbstractMapper
{
    public static function getAction(): string
    {
        return GetEmployeesAction::class;
    }

    protected static function transform(AbstractAction $action, array $response): ResponseInterface
    {
        $collection = new EmployeeCollection();
        foreach ($response['data'] as $employeeData) {
            $employee = Employee::create(
                id: (int) $employeeData['id'],
                name: $employeeData['employee_name'],
                age: (int) $employeeData['employee_age']
            );
            $collection->add($employee);
        }
        return new GetEmployeesResponse($collection);
    }
}

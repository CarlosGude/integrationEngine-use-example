<?php

declare(strict_types=1);

namespace App\Engine\Application;

use App\Engine\Domain\Employee;
use App\Engine\Infrastructure\Integrations\DummyRestApi\Dtos\Employee as EmployeeDto;
use App\Engine\Infrastructure\Integrations\DummyRestApi\DummyRestApiIntegration;
use App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployee\Response\GetEmployeeResponse;

/**
 * Anti-Corruption Layer — the only class that knows both the domain model
 * and the integration DTO. Controllers depend on App\Engine\Domain only.
 */
final class EmployeeService
{
    public function __construct(
        private readonly DummyRestApiIntegration $integration,
    ) {}

    /** @return list<Employee> */
    public function findAll(): array
    {
        $response = $this->integration->getEmployees();

        return array_map(fn(EmployeeDto $dto) => $this->toDomain($dto), $response->employees);
    }

    public function find(int $id): Employee
    {
        return $this->toDomain($this->integration->getEmployee($id)->employee);
    }

    /**
     * Returns employees keyed by the requested ID, fetched in parallel.
     *
     * @return array<int, Employee>
     */
    public function findMany(int ...$ids): array
    {
        $responses = $this->integration->getManyEmployees(...$ids);

        $employees = [];
        foreach ($responses as $id => $response) {
            \assert($response instanceof GetEmployeeResponse);
            $employees[$id] = $this->toDomain($response->employee);
        }

        return $employees;
    }

    public function create(string $name, int $salary, int $age): Employee
    {
        $response = $this->integration->createEmployee($name, $salary, $age);

        return new Employee($response->id, $response->name, $response->salary, $response->age);
    }

    private function toDomain(EmployeeDto $dto): Employee
    {
        return new Employee($dto->id, $dto->name, $dto->salary, $dto->age);
    }
}

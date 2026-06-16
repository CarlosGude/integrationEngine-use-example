<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure;

use App\Engine\Domain\Employee;
use App\Engine\Infrastructure\Integrations\DummyRestApi\Dto\Employee as EmployeeDto;
use App\Engine\Infrastructure\Integrations\DummyRestApi\DummyRestApiIntegration;
use App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployee\Response\GetEmployeeResponse;

/**
 * Anti-Corruption Layer — translates DummyRestApi integration DTOs into domain objects.
 * Lives in Infrastructure (outside Integrations/) so the integration layer stays pure.
 */
final class DummyRestApiGateway
{
    public function __construct(private readonly DummyRestApiIntegration $integration) {}

    /** @return list<Employee> */
    public function findAll(): array
    {
        return array_map(
            fn (EmployeeDto $dto) => $this->toDomain($dto),
            $this->integration->getEmployees()->employees,
        );
    }

    public function find(int $id): Employee
    {
        return $this->toDomain($this->integration->getEmployee($id)->employee);
    }

    public function create(string $name, int $salary, int $age): Employee
    {
        return $this->toDomain(
            $this->integration->createEmployee($name, $salary, $age)->employee,
        );
    }

    public function delete(int $id): void
    {
        $this->integration->deleteEmployee($id);
    }

    /**
     * Concurrent fail-fast batch — throws on the first failed request.
     *
     * @return array<int, Employee>
     */
    public function findMany(int ...$ids): array
    {
        $employees = [];

        foreach ($this->integration->getManyEmployees(...$ids) as $id => $response) {
            \assert($response instanceof GetEmployeeResponse);
            $employees[$id] = $this->toDomain($response->employee);
        }

        return $employees;
    }

    /**
     * Concurrent tolerant batch — individual failures don't abort the rest.
     *
     * @return array{employees: array<int, Employee>, errors: array<int, \Throwable>}
     */
    public function findManyResilient(int ...$ids): array
    {
        $results = $this->integration->getManyEmployeesTolerant(...$ids);

        $employees = [];

        foreach ($results->responses() as $id => $response) {
            \assert($response instanceof GetEmployeeResponse);
            $employees[$id] = $this->toDomain($response->employee);
        }

        return [
            'employees' => $employees,
            'errors' => $results->errors(),
        ];
    }

    private function toDomain(EmployeeDto $dto): Employee
    {
        return new Employee($dto->id, $dto->name, $dto->salary, $dto->age);
    }
}

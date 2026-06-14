<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployees\Response;

use App\Engine\Infrastructure\Integrations\DummyRestApi\Dtos\Employee;
use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final readonly class GetEmployeesResponse implements ResponseInterface
{
    /** @param list<Employee> $employees */
    public function __construct(public array $employees) {}

    public function toArray(): array
    {
        return array_map(fn(Employee $e) => $e->toArray(), $this->employees);
    }
}

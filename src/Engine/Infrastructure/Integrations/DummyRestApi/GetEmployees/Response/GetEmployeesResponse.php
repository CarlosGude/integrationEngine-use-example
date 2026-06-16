<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployees\Response;

use App\Engine\Infrastructure\Integrations\DummyRestApi\Dto\Employee;
use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final readonly class GetEmployeesResponse implements ResponseInterface
{
    /** @param list<Employee> $employees */
    private function __construct(public readonly array $employees) {}

    /** @param list<Employee> $employees */
    public static function create(array $employees): self
    {
        return new self($employees);
    }

    public function toArray(): array
    {
        return array_map(static fn (Employee $e) => $e->toArray(), $this->employees);
    }
}

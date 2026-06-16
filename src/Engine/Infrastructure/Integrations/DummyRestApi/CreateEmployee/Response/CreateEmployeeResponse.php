<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\DummyRestApi\CreateEmployee\Response;

use App\Engine\Infrastructure\Integrations\DummyRestApi\Dto\Employee;
use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final readonly class CreateEmployeeResponse implements ResponseInterface
{
    private function __construct(public readonly Employee $employee) {}

    public static function create(Employee $employee): self
    {
        return new self($employee);
    }

    public function toArray(): array
    {
        return $this->employee->toArray();
    }
}

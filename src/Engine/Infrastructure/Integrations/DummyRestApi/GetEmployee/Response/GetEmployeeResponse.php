<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployee\Response;

use App\Engine\Infrastructure\Integrations\DummyRestApi\Dtos\Employee;
use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final readonly class GetEmployeeResponse implements ResponseInterface
{
    public function __construct(public Employee $employee) {}

    public function toArray(): array
    {
        return $this->employee->toArray();
    }
}

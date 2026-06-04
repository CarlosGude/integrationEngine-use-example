<?php

declare(strict_types=1);

namespace App\Infrastructure\Integrations\DummyRestApi\GetEmployee\Response;

use App\Infrastructure\Integrations\DummyRestApi\Dtos\Employee;
use IntegrationEngine\Core\Contract\ResponseInterface;

final readonly class GetEmployeeResponse implements ResponseInterface
{
    public function __construct(
        private Employee $employee
    )
    {

    }
    public function toArray(): array
    {
        return $this->employee->toArray();
    }
}

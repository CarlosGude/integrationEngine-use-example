<?php

declare(strict_types=1);

namespace App\Infrastructure\Integrations\DummyRestApi\GetEmployees\Response;

use App\Infrastructure\Integrations\DummyRestApi\Collections\EmployeeCollection;
use IntegrationEngine\Core\Contract\ResponseInterface;

final readonly class GetEmployeesResponse implements ResponseInterface
{
    public function __construct(
        protected EmployeeCollection $employees
    )
    {}
    public function toArray(): array
    {
        return $this->employees->toArray();
    }
}

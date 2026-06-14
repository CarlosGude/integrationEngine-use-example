<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\DummyRestApi\CreateEmployee\Response;

use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final readonly class CreateEmployeeResponse implements ResponseInterface
{
    public function __construct(
        public int $id,
        public string $name,
        public int $salary,
        public int $age,
    ) {}

    public function toArray(): array
    {
        return [
            'id'     => $this->id,
            'name'   => $this->name,
            'salary' => $this->salary,
            'age'    => $this->age,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\DummyRestApi\CreateEmployee\Request;

use IntegrationEngine\Core\Contract\Action\ActionBodyInterface;

final readonly class CreateEmployeeBody implements ActionBodyInterface
{
    private function __construct(
        public string $name,
        public string $salary,
        public string $age,
    ) {}

    public static function create(array $data): self
    {
        return new self(
            name:   $data['name'],
            salary: $data['salary'],
            age:    $data['age'],
        );
    }

    public function toArray(): array
    {
        return [
            'name'   => $this->name,
            'salary' => $this->salary,
            'age'    => $this->age,
        ];
    }
}

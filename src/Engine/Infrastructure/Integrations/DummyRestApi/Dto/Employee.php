<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\DummyRestApi\Dto;

final readonly class Employee
{
    private function __construct(
        public int $id,
        public string $name,
        public int $salary,
        public int $age,
    ) {}

    /** @param array<string, mixed> $data */
    public static function create(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['employee_name'] ?? $data['name'] ?? ''),
            salary: (int) ($data['employee_salary'] ?? $data['salary'] ?? 0),
            age: (int) ($data['employee_age'] ?? $data['age'] ?? 0),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'salary' => $this->salary,
            'age' => $this->age,
        ];
    }
}

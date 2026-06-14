<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\DummyRestApi\Dtos;

final readonly class Employee
{
    private function __construct(
        public int $id,
        public string $name,
        public int $salary,
        public int $age,
    ) {}

    public static function create(int $id, string $name, int $salary, int $age): self
    {
        return new self($id, $name, $salary, $age);
    }

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

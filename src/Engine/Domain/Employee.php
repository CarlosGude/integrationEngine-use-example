<?php

declare(strict_types=1);

namespace App\Engine\Domain;

final readonly class Employee
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
            'id' => $this->id,
            'name' => $this->name,
            'salary' => $this->salary,
            'age' => $this->age,
        ];
    }
}

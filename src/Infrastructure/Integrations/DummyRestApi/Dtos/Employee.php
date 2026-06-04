<?php

namespace App\Infrastructure\Integrations\DummyRestApi\Dtos;

final readonly class Employee
{
    protected function __construct(
        protected int $id,
        protected string $name,
        protected int $age
    )
    {

    }

    public static function create(int $id, string $name, int $age): self
    {
        return new self($id, $name, $age);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'age' => $this->getAge(),
        ];
    }


}

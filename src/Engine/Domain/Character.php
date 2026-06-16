<?php

declare(strict_types=1);

namespace App\Engine\Domain;

final readonly class Character
{
    public function __construct(
        public int $id,
        public string $name,
        public string $status,
        public string $species,
        public string $gender,
        public string $image,
        public string $origin,
        public string $location,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'species' => $this->species,
            'gender' => $this->gender,
            'image' => $this->image,
            'origin' => $this->origin,
            'location' => $this->location,
        ];
    }
}

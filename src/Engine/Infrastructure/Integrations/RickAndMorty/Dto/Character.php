<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\RickAndMorty\Dto;

final readonly class Character
{
    private function __construct(
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

    /** @param array<string, mixed> $data */
    public static function create(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            status: (string) ($data['status'] ?? ''),
            species: (string) ($data['species'] ?? ''),
            gender: (string) ($data['gender'] ?? ''),
            image: (string) ($data['image'] ?? ''),
            origin: (string) ($data['origin']['name'] ?? ''),
            location: (string) ($data['location']['name'] ?? ''),
        );
    }
}

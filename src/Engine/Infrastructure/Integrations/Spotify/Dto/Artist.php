<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\Spotify\Dto;

final readonly class Artist
{
    private function __construct(
        public string $id,
        public string $name,
        public array $genres,
        public int $popularity,
        public int $followers,
        public string $imageUrl,
    ) {}

    /** @param array<string, mixed> $data */
    public static function create(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            genres: ($data['genres'] ?? []),
            popularity: (int) ($data['popularity'] ?? 0),
            followers: (int) ($data['followers']['total'] ?? 0),
            imageUrl: (string) ($data['images'][0]['url'] ?? ''),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'genres' => $this->genres,
            'popularity' => $this->popularity,
            'followers' => $this->followers,
            'image_url' => $this->imageUrl,
        ];
    }
}

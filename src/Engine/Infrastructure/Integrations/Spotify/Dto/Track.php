<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\Spotify\Dto;

final readonly class Track
{
    private function __construct(
        public string $id,
        public string $name,
        public int $durationMs,
        public string $albumName,
        public ?string $previewUrl,
    ) {}

    /** @param array<string, mixed> $data */
    public static function create(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            durationMs: (int) ($data['duration_ms'] ?? 0),
            albumName: (string) ($data['album']['name'] ?? ''),
            previewUrl: isset($data['preview_url']) ? (string) $data['preview_url'] : null,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'duration_ms' => $this->durationMs,
            'album' => $this->albumName,
            'preview_url' => $this->previewUrl,
        ];
    }
}

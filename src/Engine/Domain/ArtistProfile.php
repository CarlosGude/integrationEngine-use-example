<?php

declare(strict_types=1);

namespace App\Engine\Domain;

final readonly class ArtistProfile
{
    /**
     * @param list<string>              $genres
     * @param list<array<string,mixed>> $topTracks
     */
    public function __construct(
        public string $id,
        public string $name,
        public array $genres,
        public int $popularity,
        public int $followers,
        public string $imageUrl,
        public array $topTracks,
    ) {}

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
            'top_tracks' => $this->topTracks,
        ];
    }
}

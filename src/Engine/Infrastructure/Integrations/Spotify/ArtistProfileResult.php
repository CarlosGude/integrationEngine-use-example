<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\Spotify;

use App\Engine\Infrastructure\Integrations\Spotify\Dto\Artist;
use App\Engine\Infrastructure\Integrations\Spotify\Dto\Track;

final readonly class ArtistProfileResult
{
    /** @param list<Track> $topTracks */
    public function __construct(
        public Artist $artist,
        public array $topTracks,
    ) {}
}

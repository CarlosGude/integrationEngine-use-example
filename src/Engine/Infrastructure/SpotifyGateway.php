<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure;

use App\Engine\Domain\ArtistProfile;
use App\Engine\Infrastructure\Integrations\Spotify\Dto\Track;
use App\Engine\Infrastructure\Integrations\Spotify\SpotifyIntegration;

/**
 * Anti-Corruption Layer — translates Spotify integration DTOs into domain objects.
 * Lives in Infrastructure (outside Integrations/) so the integration layer stays pure.
 */
final class SpotifyGateway
{
    public function __construct(private readonly SpotifyIntegration $integration) {}

    public function getArtistProfile(string $artistId): ArtistProfile
    {
        $result = $this->integration->getArtistProfile($artistId);
        $artist = $result->artist;

        return new ArtistProfile(
            id: $artist->id,
            name: $artist->name,
            genres: $artist->genres,
            popularity: $artist->popularity,
            followers: $artist->followers,
            imageUrl: $artist->imageUrl,
            topTracks: array_map(static fn (Track $t) => $t->toArray(), $result->topTracks),
        );
    }
}

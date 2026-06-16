<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\Spotify\GetArtistTopTracks\Response;

use App\Engine\Infrastructure\Integrations\Spotify\Dto\Track;
use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final readonly class GetArtistTopTracksResponse implements ResponseInterface
{
    /** @param list<Track> $tracks */
    private function __construct(public readonly array $tracks) {}

    /** @param list<Track> $tracks */
    public static function create(array $tracks): self
    {
        return new self($tracks);
    }

    public function toArray(): array
    {
        return array_map(static fn (Track $t) => $t->toArray(), $this->tracks);
    }
}

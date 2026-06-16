<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\Spotify\GetArtist\Response;

use App\Engine\Infrastructure\Integrations\Spotify\Dto\Artist;
use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final readonly class GetArtistResponse implements ResponseInterface
{
    private function __construct(public readonly Artist $artist) {}

    public static function create(Artist $artist): self
    {
        return new self($artist);
    }

    public function toArray(): array
    {
        return $this->artist->toArray();
    }
}

<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\Spotify\GetArtistTopTracks\Response;

use App\Engine\Infrastructure\Integrations\Spotify\Dto\Track;
use App\Engine\Infrastructure\Integrations\Spotify\GetArtistTopTracks\Request\GetArtistTopTracksAction;
use IntegrationEngine\Core\Contract\Action\AbstractAction;
use IntegrationEngine\Core\Contract\Mapper\AbstractMapper;
use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final class GetArtistTopTracksMapper extends AbstractMapper
{
    public static function getAction(): string
    {
        return GetArtistTopTracksAction::class;
    }

    protected static function transform(AbstractAction $action, array $response): ResponseInterface
    {
        $tracks = array_map(
            static fn (array $data): Track => Track::create($data),
            $response['tracks'] ?? [],
        );

        return GetArtistTopTracksResponse::create($tracks);
    }
}

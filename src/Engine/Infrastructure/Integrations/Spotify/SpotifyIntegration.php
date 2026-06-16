<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\Spotify;

use App\Engine\Infrastructure\Integrations\Spotify\GetArtist\Request\GetArtistAction;
use App\Engine\Infrastructure\Integrations\Spotify\GetArtist\Response\GetArtistResponse;
use App\Engine\Infrastructure\Integrations\Spotify\GetArtistTopTracks\Request\GetArtistTopTracksAction;
use App\Engine\Infrastructure\Integrations\Spotify\GetArtistTopTracks\Response\GetArtistTopTracksResponse;
use IntegrationEngine\Core\Batch\EngineRequest;
use IntegrationEngine\Core\Contract\Action\DefaultActionContext;
use IntegrationEngine\Core\IntegrationEngine;
use IntegrationEngine\Core\Registry\IntegrationName;
use IntegrationEngine\Core\Registry\IntegrationRegistry;

final class SpotifyIntegration implements IntegrationName
{
    public const string NAME = 'spotify';

    private IntegrationEngine $engine;

    public function __construct(IntegrationRegistry $registry)
    {
        $this->engine = $registry->get(self::NAME);
    }

    /**
     * Fetches artist profile and top tracks concurrently in a single batch.
     * Both requests are dispatched simultaneously — total time ≈ the slower of the two.
     */
    public function getArtistProfile(string $artistId): ArtistProfileResult
    {
        $context = DefaultActionContext::create(['id' => $artistId]);

        $results = $this->engine->sendMany([
            'artist' => EngineRequest::create(GetArtistAction::getName(), $context),
            'top_tracks' => EngineRequest::create(GetArtistTopTracksAction::getName(), $context),
        ]);

        $artistResponse = $results['artist']->response();
        $topTracksResponse = $results['top_tracks']->response();

        \assert($artistResponse instanceof GetArtistResponse);
        \assert($topTracksResponse instanceof GetArtistTopTracksResponse);

        return new ArtistProfileResult(
            artist: $artistResponse->artist,
            topTracks: $topTracksResponse->tracks,
        );
    }
}

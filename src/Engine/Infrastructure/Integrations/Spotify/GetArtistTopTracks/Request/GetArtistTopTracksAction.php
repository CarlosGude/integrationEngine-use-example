<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\Spotify\GetArtistTopTracks\Request;

use App\Engine\Infrastructure\Integrations\Spotify\GetArtistTopTracks\Response\GetArtistTopTracksMapper;
use IntegrationEngine\Core\Contract\Action\AbstractAction;

final class GetArtistTopTracksAction extends AbstractAction
{
    public static function getName(): string
    {
        return 'GetArtistTopTracks';
    }

    public static function hasResponse(): bool
    {
        return true;
    }

    public static function mapper(): ?string
    {
        return GetArtistTopTracksMapper::class;
    }
}

<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\Spotify\GetArtist\Request;

use App\Engine\Infrastructure\Integrations\Spotify\GetArtist\Response\GetArtistMapper;
use IntegrationEngine\Core\Contract\Action\AbstractAction;

final class GetArtistAction extends AbstractAction
{
    public static function getName(): string
    {
        return 'GetArtist';
    }

    public static function hasResponse(): bool
    {
        return true;
    }

    public static function mapper(): ?string
    {
        return GetArtistMapper::class;
    }
}

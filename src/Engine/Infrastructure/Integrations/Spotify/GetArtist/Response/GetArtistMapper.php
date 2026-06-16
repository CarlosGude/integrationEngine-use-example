<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\Spotify\GetArtist\Response;

use App\Engine\Infrastructure\Integrations\Spotify\Dto\Artist;
use App\Engine\Infrastructure\Integrations\Spotify\GetArtist\Request\GetArtistAction;
use IntegrationEngine\Core\Contract\Action\AbstractAction;
use IntegrationEngine\Core\Contract\Mapper\AbstractMapper;
use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final class GetArtistMapper extends AbstractMapper
{
    public static function getAction(): string
    {
        return GetArtistAction::class;
    }

    protected static function transform(AbstractAction $action, array $response): ResponseInterface
    {
        return GetArtistResponse::create(
            Artist::create($response),
        );
    }
}

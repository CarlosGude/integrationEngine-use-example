<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\Spotify\FetchToken\Response;

use App\Engine\Infrastructure\Integrations\Spotify\FetchToken\Request\FetchTokenAction;
use IntegrationEngine\Core\Contract\Action\AbstractAction;
use IntegrationEngine\Core\Contract\Mapper\AbstractMapper;
use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final class FetchTokenMapper extends AbstractMapper
{
    public static function getAction(): string
    {
        return FetchTokenAction::class;
    }

    protected static function transform(AbstractAction $action, array $response): ResponseInterface
    {
        return FetchTokenResponse::create(
            (string) ($response['access_token'] ?? ''),
        );
    }
}

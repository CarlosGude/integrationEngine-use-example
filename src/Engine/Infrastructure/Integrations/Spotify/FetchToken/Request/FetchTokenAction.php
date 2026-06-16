<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\Spotify\FetchToken\Request;

use App\Engine\Infrastructure\Integrations\Spotify\FetchToken\Response\FetchTokenMapper;
use IntegrationEngine\Core\Contract\Action\AbstractAction;

/**
 * Spotify OAuth2 Client Credentials token action.
 *
 * Demo note: Spotify's token endpoint requires Content-Type: application/x-www-form-urlencoded
 * with body grant_type=client_credentials. The engine sends JSON by default, so this
 * action cannot authenticate with real Spotify credentials as-is.
 *
 * What this class does show:
 *  - How to structure a token action for dynamic auth
 *  - How FetchTokenMapper extracts access_token from the response
 *  - How "type: dynamic / action: FetchToken" in Spotify.yaml wires it all together
 *
 * Production fix: implement ClientAdapterInterface with form-encoded body support
 * and register it via client_service in integration_engine.yaml.
 */
final class FetchTokenAction extends AbstractAction
{
    public static function getName(): string
    {
        return 'FetchToken';
    }

    public static function hasResponse(): bool
    {
        return true;
    }

    public static function mapper(): ?string
    {
        return FetchTokenMapper::class;
    }
}

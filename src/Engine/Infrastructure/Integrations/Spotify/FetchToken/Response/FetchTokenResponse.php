<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\Spotify\FetchToken\Response;

use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final readonly class FetchTokenResponse implements ResponseInterface
{
    private function __construct(public readonly string $accessToken) {}

    public static function create(string $accessToken): self
    {
        return new self($accessToken);
    }

    public function toArray(): array
    {
        return ['access_token' => $this->accessToken];
    }
}

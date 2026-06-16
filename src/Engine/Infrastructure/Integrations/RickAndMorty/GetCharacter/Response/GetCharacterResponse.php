<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\RickAndMorty\GetCharacter\Response;

use App\Engine\Infrastructure\Integrations\RickAndMorty\Dto\Character;
use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final readonly class GetCharacterResponse implements ResponseInterface
{
    private function __construct(public readonly Character $character) {}

    public static function create(Character $character): self
    {
        return new self($character);
    }

    public function toArray(): array
    {
        return $this->character->toArray();
    }
}

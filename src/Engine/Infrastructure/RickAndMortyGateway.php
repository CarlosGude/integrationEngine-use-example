<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure;

use App\Engine\Domain\Character;
use App\Engine\Infrastructure\Integrations\RickAndMorty\Dto\Character as CharacterDto;
use App\Engine\Infrastructure\Integrations\RickAndMorty\GetCharacter\Response\GetCharacterResponse;
use App\Engine\Infrastructure\Integrations\RickAndMorty\RickAndMortyIntegration;

/**
 * Anti-Corruption Layer — translates RickAndMorty integration DTOs into domain objects.
 * Lives in Infrastructure (outside Integrations/) so the integration layer stays pure.
 */
final class RickAndMortyGateway
{
    public function __construct(private readonly RickAndMortyIntegration $integration) {}

    public function getCharacter(int $id): Character
    {
        return $this->toDomain($this->integration->getCharacter($id)->character);
    }

    /**
     * @return array<int, Character>
     */
    public function getManyCharacters(int ...$ids): array
    {
        $characters = [];

        foreach ($this->integration->getManyCharacters(...$ids) as $id => $response) {
            \assert($response instanceof GetCharacterResponse);
            $characters[$id] = $this->toDomain($response->character);
        }

        return $characters;
    }

    private function toDomain(CharacterDto $dto): Character
    {
        return new Character(
            id: $dto->id,
            name: $dto->name,
            status: $dto->status,
            species: $dto->species,
            gender: $dto->gender,
            image: $dto->image,
            origin: $dto->origin,
            location: $dto->location,
        );
    }
}

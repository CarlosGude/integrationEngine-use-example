<?php

declare(strict_types=1);

namespace App\Tests\Engine\Infrastructure\Integrations\RickAndMorty\GetCharacter\Response;

use App\Engine\Infrastructure\Integrations\RickAndMorty\GetCharacter\Request\GetCharacterAction;
use App\Engine\Infrastructure\Integrations\RickAndMorty\GetCharacter\Response\GetCharacterMapper;
use App\Engine\Infrastructure\Integrations\RickAndMorty\GetCharacter\Response\GetCharacterResponse;
use PHPUnit\Framework\TestCase;

/**
 * GraphQL mappers receive pre-unwrapped data — GraphQLClientAdapter strips data.data
 * before calling the mapper, so the input here mirrors what the mapper actually sees.
 */
final class GetCharacterMapperTest extends TestCase
{
    public function testMapsUnwrappedGraphQLPayloadToTypedResponse(): void
    {
        // GraphQL client POSTs {query, variables} and receives:
        //   {"data": {"data": {"character": {...}}}}
        // GraphQLClientAdapter unwraps data.data — the mapper receives:
        //   {"character": {...}}
        $action = GetCharacterAction::create('', '');

        $response = GetCharacterMapper::map($action, [
            'character' => [
                'id'       => 1,
                'name'     => 'Rick Sanchez',
                'status'   => 'Alive',
                'species'  => 'Human',
                'gender'   => 'Male',
                'image'    => 'https://rickandmortyapi.com/api/character/avatar/1.jpeg',
                'origin'   => ['name' => 'Earth (C-137)'],
                'location' => ['name' => 'Citadel of Ricks'],
            ],
        ]);

        self::assertInstanceOf(GetCharacterResponse::class, $response);
        self::assertSame(1, $response->character->id);
        self::assertSame('Rick Sanchez', $response->character->name);
        // Nested objects are flattened by Character::create()
        self::assertSame('Earth (C-137)', $response->character->origin);
        self::assertSame('Citadel of Ricks', $response->character->location);
    }
}

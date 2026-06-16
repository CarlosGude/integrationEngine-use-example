<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\RickAndMorty\GetCharacter\Response;

use App\Engine\Infrastructure\Integrations\RickAndMorty\Dto\Character;
use App\Engine\Infrastructure\Integrations\RickAndMorty\GetCharacter\Request\GetCharacterAction;
use IntegrationEngine\Core\Contract\Action\AbstractAction;
use IntegrationEngine\Core\Contract\Mapper\AbstractMapper;
use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final class GetCharacterMapper extends AbstractMapper
{
    public static function getAction(): string
    {
        return GetCharacterAction::class;
    }

    protected static function transform(AbstractAction $action, array $response): ResponseInterface
    {
        // GraphQLClientAdapter already unwraps data.data → we receive ['character' => {...}]
        return GetCharacterResponse::create(
            Character::create($response['character'] ?? []),
        );
    }
}

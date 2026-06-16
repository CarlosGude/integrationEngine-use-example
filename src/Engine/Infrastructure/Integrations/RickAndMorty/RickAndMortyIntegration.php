<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\RickAndMorty;

use App\Engine\Infrastructure\Integrations\RickAndMorty\GetCharacter\Request\GetCharacterAction;
use App\Engine\Infrastructure\Integrations\RickAndMorty\GetCharacter\Request\GetCharacterBody;
use App\Engine\Infrastructure\Integrations\RickAndMorty\GetCharacter\Response\GetCharacterResponse;
use IntegrationEngine\Core\Batch\EngineRequest;
use IntegrationEngine\Core\Contract\Response\ResponseInterface;
use IntegrationEngine\Core\IntegrationEngine;
use IntegrationEngine\Core\Registry\IntegrationName;
use IntegrationEngine\Core\Registry\IntegrationRegistry;

final class RickAndMortyIntegration implements IntegrationName
{
    public const string NAME = 'rick_and_morty';

    private IntegrationEngine $engine;

    public function __construct(IntegrationRegistry $registry)
    {
        $this->engine = $registry->get(self::NAME);
    }

    public function getCharacter(int $id): GetCharacterResponse
    {
        $response = $this->engine->send(
            actionName: GetCharacterAction::getName(),
            body: GetCharacterBody::create(['id' => $id]),
        );

        \assert($response instanceof GetCharacterResponse);

        return $response;
    }

    /**
     * Fetches N characters concurrently via sendManyOrFail().
     * Total time ≈ slowest single request, regardless of how many IDs are passed.
     *
     * @return array<int, ResponseInterface>
     */
    public function getManyCharacters(int ...$ids): array
    {
        $requests = [];

        foreach ($ids as $id) {
            $requests[$id] = EngineRequest::create(
                actionName: GetCharacterAction::getName(),
                body: GetCharacterBody::create(['id' => $id]),
            );
        }

        return $this->engine->sendManyOrFail($requests);
    }
}

<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\RickAndMorty\GetCharacter\Request;

use IntegrationEngine\Core\Contract\Action\GraphQLBodyInterface;

final readonly class GetCharacterBody implements GraphQLBodyInterface
{
    private function __construct(private int $id) {}

    public static function create(array $data): self
    {
        return new self((int) ($data['id'] ?? 0));
    }

    public function getQuery(): string
    {
        return <<<'GQL'
            query GetCharacter($id: ID!) {
                character(id: $id) {
                    id
                    name
                    status
                    species
                    gender
                    image
                    origin   { name }
                    location { name }
                }
            }
            GQL;
    }

    public function getVariables(): array
    {
        return ['id' => (string) $this->id];
    }

    public function toArray(): array
    {
        return ['query' => $this->getQuery(), 'variables' => $this->getVariables()];
    }
}

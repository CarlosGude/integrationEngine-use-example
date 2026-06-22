<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure;

use App\Engine\Domain\Post;
use App\Engine\Infrastructure\Integrations\Posts\Dto\Post as PostDto;
use App\Engine\Infrastructure\Integrations\Posts\PostsIntegration;

/**
 * Anti-Corruption Layer — translates Posts integration DTOs into domain objects.
 * Lives in Infrastructure (outside Integrations/) so the integration layer stays pure.
 */
final class PostsGateway
{
    public function __construct(private readonly PostsIntegration $integration) {}

    /**
     * $host is forwarded as-is to PostsIntegration::getPost() — null keeps
     * the configured base_url; any other value overrides it for this call.
     */
    public function find(int $id, ?string $host = null): Post
    {
        return $this->toDomain($this->integration->getPost($id, $host)->post);
    }

    private function toDomain(PostDto $dto): Post
    {
        return new Post($dto->id, $dto->title, $dto->body);
    }
}

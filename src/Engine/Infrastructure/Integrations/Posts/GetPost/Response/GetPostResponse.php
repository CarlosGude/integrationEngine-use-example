<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\Posts\GetPost\Response;

use App\Engine\Infrastructure\Integrations\Posts\Dto\Post;
use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final readonly class GetPostResponse implements ResponseInterface
{
    private function __construct(public readonly Post $post) {}

    public static function create(Post $post): self
    {
        return new self($post);
    }

    public function toArray(): array
    {
        return $this->post->toArray();
    }
}

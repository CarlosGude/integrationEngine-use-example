<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\Posts\Dto;

final readonly class Post
{
    private function __construct(
        public int $id,
        public string $title,
        public string $body,
    ) {}

    /** @param array<string, mixed> $data */
    public static function create(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            title: (string) ($data['title'] ?? ''),
            // The default host returns a "body"; the alternate demo host does not.
            body: (string) ($data['body'] ?? ''),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
        ];
    }
}

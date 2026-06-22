<?php

declare(strict_types=1);

namespace App\Engine\Domain;

final readonly class Post
{
    public function __construct(
        public int $id,
        public string $title,
        public string $body,
    ) {}

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

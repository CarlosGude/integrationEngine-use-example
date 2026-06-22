<?php

declare(strict_types=1);

namespace App\Tests\Engine\Infrastructure\Integrations\Posts\GetPost\Response;

use App\Engine\Infrastructure\Integrations\Posts\GetPost\Request\GetPostAction;
use App\Engine\Infrastructure\Integrations\Posts\GetPost\Response\GetPostMapper;
use App\Engine\Infrastructure\Integrations\Posts\GetPost\Response\GetPostResponse;
use PHPUnit\Framework\TestCase;

/**
 * Mappers are pure static functions — no engine needed to test them.
 * Call AbstractMapper::map() directly with the raw API response shape.
 */
final class GetPostMapperTest extends TestCase
{
    public function testMapsRawRestResponseToTypedResponse(): void
    {
        $action = GetPostAction::create('GET', '/posts/1');

        $response = GetPostMapper::map($action, [
            'id' => 1,
            'title' => 'sunt aut facere',
            'body' => 'quia et suscipit',
        ]);

        self::assertInstanceOf(GetPostResponse::class, $response);
        self::assertSame(1, $response->post->id);
        self::assertSame('sunt aut facere', $response->post->title);
        self::assertSame('quia et suscipit', $response->post->body);
    }

    public function testMapsAlternateHostShapeWithoutBodyField(): void
    {
        // The alternate demo host (my-json-server) only returns id + title.
        $action = GetPostAction::create('GET', '/posts/1');

        $response = GetPostMapper::map($action, [
            'id' => 1,
            'title' => 'Post 1',
        ]);

        self::assertInstanceOf(GetPostResponse::class, $response);
        self::assertSame('Post 1', $response->post->title);
        self::assertSame('', $response->post->body);
    }
}

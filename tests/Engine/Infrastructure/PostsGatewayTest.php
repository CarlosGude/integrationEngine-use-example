<?php

declare(strict_types=1);

namespace App\Tests\Engine\Infrastructure;

use App\Engine\Domain\Post;
use App\Engine\Infrastructure\Integrations\Posts\Dto\Post as PostDto;
use App\Engine\Infrastructure\Integrations\Posts\GetPost\Response\GetPostResponse;
use App\Engine\Infrastructure\Integrations\Posts\PostsIntegration;
use App\Engine\Infrastructure\PostsGateway;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Anti-Corruption Layer in isolation.
 * Mock the integration facade → verify domain objects come out the other side.
 */
final class PostsGatewayTest extends TestCase
{
    public function testFindTranslatesDtoToDomainObjectWithoutAHostOverride(): void
    {
        $integration = $this->createMock(PostsIntegration::class);
        $integration->method('getPost')->with(1, null)->willReturn(
            GetPostResponse::create(PostDto::create(['id' => 1, 'title' => 'sunt aut facere', 'body' => 'quia et suscipit'])),
        );

        $gateway = new PostsGateway($integration);
        $post = $gateway->find(1);

        self::assertInstanceOf(Post::class, $post);
        self::assertSame(1, $post->id);
        self::assertSame('sunt aut facere', $post->title);
    }

    /**
     * Proves the per-request host override reaches the integration facade
     * unchanged — this is the contract IntegrationEngine::send(baseUrl:)
     * relies on: the gateway/integration layer never resolves or caches it.
     */
    public function testFindForwardsAnExplicitHostOverrideToTheIntegration(): void
    {
        $integration = $this->createMock(PostsIntegration::class);
        $integration->method('getPost')
            ->with(1, 'https://my-json-server.typicode.com/typicode/demo')
            ->willReturn(GetPostResponse::create(PostDto::create(['id' => 1, 'title' => 'Post 1'])))
        ;

        $gateway = new PostsGateway($integration);
        $post = $gateway->find(1, 'https://my-json-server.typicode.com/typicode/demo');

        self::assertSame('Post 1', $post->title);
        self::assertSame('', $post->body);
    }
}

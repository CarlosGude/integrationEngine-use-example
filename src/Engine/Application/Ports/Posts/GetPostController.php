<?php

declare(strict_types=1);

namespace App\Engine\Application\Ports\Posts;

use App\Engine\Infrastructure\PostsGateway;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Demonstrates IntegrationEngine::send(baseUrl: ...): the configured
 * base_url for "posts" is https://jsonplaceholder.typicode.com. Passing
 * ?host=<url> overrides it for this one request only — no config change,
 * no recompiled container.
 *
 * Try both:
 *   GET /engine/posts/1
 *   GET /engine/posts/1?host=https://my-json-server.typicode.com/typicode/demo
 */
final class GetPostController extends AbstractController
{
    public function __construct(private readonly PostsGateway $gateway) {}

    #[Route('/engine/posts/{id}', name: 'engine_post_get', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function __invoke(int $id, Request $request): JsonResponse
    {
        $host = $request->query->get('host');

        return new JsonResponse([
            'host_used' => $host ?? '(configured base_url)',
            'post' => $this->gateway->find($id, $host)->toArray(),
        ]);
    }
}

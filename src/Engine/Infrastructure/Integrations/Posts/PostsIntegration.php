<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\Posts;

use App\Engine\Infrastructure\Integrations\Posts\GetPost\Request\GetPostAction;
use App\Engine\Infrastructure\Integrations\Posts\GetPost\Response\GetPostResponse;
use IntegrationEngine\Core\Contract\Action\DefaultActionContext;
use IntegrationEngine\Core\IntegrationEngine;
use IntegrationEngine\Core\Registry\IntegrationName;
use IntegrationEngine\Core\Registry\IntegrationRegistry;

/**
 * Demonstrates IntegrationEngine::send()'s optional per-request $baseUrl.
 *
 * The configured base_url (integration_engine.yaml) is the default host.
 * Passing $host here overrides it for this single call only — nothing is
 * persisted, nothing is cached. The integration has no idea what $host
 * represents (tenant domain, regional mirror, ...); resolving that meaning
 * is the caller's job, same as the bundle's design intends.
 */
class PostsIntegration implements IntegrationName
{
    public const string NAME = 'posts';

    private IntegrationEngine $engine;

    public function __construct(IntegrationRegistry $registry)
    {
        $this->engine = $registry->get(self::NAME);
    }

    public function getPost(int $id, ?string $host = null): GetPostResponse
    {
        $response = $this->engine->send(
            actionName: GetPostAction::getName(),
            context: DefaultActionContext::create(['id' => $id]),
            baseUrl: $host,
        );

        \assert($response instanceof GetPostResponse);

        return $response;
    }
}

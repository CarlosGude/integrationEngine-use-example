<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\Posts\GetPost\Response;

use App\Engine\Infrastructure\Integrations\Posts\Dto\Post;
use App\Engine\Infrastructure\Integrations\Posts\GetPost\Request\GetPostAction;
use IntegrationEngine\Core\Contract\Action\AbstractAction;
use IntegrationEngine\Core\Contract\Mapper\AbstractMapper;
use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final class GetPostMapper extends AbstractMapper
{
    public static function getAction(): string
    {
        return GetPostAction::class;
    }

    protected static function transform(AbstractAction $action, array $response): ResponseInterface
    {
        return GetPostResponse::create(Post::create($response));
    }
}

<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\Posts\GetPost\Request;

use App\Engine\Infrastructure\Integrations\Posts\GetPost\Response\GetPostMapper;
use IntegrationEngine\Core\Contract\Action\AbstractAction;

final class GetPostAction extends AbstractAction
{
    public static function getName(): string
    {
        return 'GetPost';
    }

    public static function hasResponse(): bool
    {
        return true;
    }

    public static function mapper(): ?string
    {
        return GetPostMapper::class;
    }
}

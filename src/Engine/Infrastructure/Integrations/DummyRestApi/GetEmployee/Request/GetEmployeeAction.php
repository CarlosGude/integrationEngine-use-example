<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployee\Request;

use App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployee\Response\GetEmployeeMapper;
use IntegrationEngine\Core\Contract\Action\AbstractAction;

final class GetEmployeeAction extends AbstractAction
{
    public static function getName(): string
    {
        return 'GetEmployee';
    }

    public static function hasResponse(): bool
    {
        return true;
    }

    public static function mapper(): ?string
    {
        return GetEmployeeMapper::class;
    }
}

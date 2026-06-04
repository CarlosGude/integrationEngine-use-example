<?php

declare(strict_types=1);

namespace App\Infrastructure\Integrations\DummyRestApi\GetEmployee\Request;

use IntegrationEngine\Core\Contract\AbstractAction;
use App\Infrastructure\Integrations\DummyRestApi\GetEmployee\Response\GetEmployeeMapper;

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
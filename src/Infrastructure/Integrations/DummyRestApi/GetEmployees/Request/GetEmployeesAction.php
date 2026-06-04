<?php

declare(strict_types=1);

namespace App\Infrastructure\Integrations\DummyRestApi\GetEmployees\Request;

use IntegrationEngine\Core\Contract\AbstractAction;
use App\Infrastructure\Integrations\DummyRestApi\GetEmployees\Response\GetEmployeesMapper;

final class GetEmployeesAction extends AbstractAction
{
    public static function getName(): string
    {
        return 'GetEmployees';
    }

    public static function hasResponse(): bool
    {
        return true;
    }

    public static function mapper(): ?string
    {
        return GetEmployeesMapper::class;
    }
}
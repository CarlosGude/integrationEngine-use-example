<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployees\Request;

use App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployees\Response\GetEmployeesMapper;
use IntegrationEngine\Core\Contract\Action\AbstractAction;

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

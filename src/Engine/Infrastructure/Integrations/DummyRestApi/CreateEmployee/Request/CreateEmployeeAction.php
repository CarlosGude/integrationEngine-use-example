<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\DummyRestApi\CreateEmployee\Request;

use App\Engine\Infrastructure\Integrations\DummyRestApi\CreateEmployee\Response\CreateEmployeeMapper;
use IntegrationEngine\Core\Contract\Action\AbstractAction;

final class CreateEmployeeAction extends AbstractAction
{
    public static function getName(): string
    {
        return 'CreateEmployee';
    }

    public static function hasResponse(): bool
    {
        return true;
    }

    public static function mapper(): ?string
    {
        return CreateEmployeeMapper::class;
    }
}

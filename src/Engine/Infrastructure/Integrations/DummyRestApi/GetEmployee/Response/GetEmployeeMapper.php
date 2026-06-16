<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployee\Response;

use App\Engine\Infrastructure\Integrations\DummyRestApi\Dto\Employee;
use App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployee\Request\GetEmployeeAction;
use IntegrationEngine\Core\Contract\Action\AbstractAction;
use IntegrationEngine\Core\Contract\Mapper\AbstractMapper;
use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final class GetEmployeeMapper extends AbstractMapper
{
    public static function getAction(): string
    {
        return GetEmployeeAction::class;
    }

    protected static function transform(AbstractAction $action, array $response): ResponseInterface
    {
        return GetEmployeeResponse::create(
            Employee::create($response['data'] ?? []),
        );
    }
}

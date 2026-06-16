<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\DummyRestApi\CreateEmployee\Response;

use App\Engine\Infrastructure\Integrations\DummyRestApi\CreateEmployee\Request\CreateEmployeeAction;
use App\Engine\Infrastructure\Integrations\DummyRestApi\Dto\Employee;
use IntegrationEngine\Core\Contract\Action\AbstractAction;
use IntegrationEngine\Core\Contract\Mapper\AbstractMapper;
use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final class CreateEmployeeMapper extends AbstractMapper
{
    public static function getAction(): string
    {
        return CreateEmployeeAction::class;
    }

    protected static function transform(AbstractAction $action, array $response): ResponseInterface
    {
        $data = $response['data'] ?? [];

        return CreateEmployeeResponse::create(
            Employee::create($data),
        );
    }
}

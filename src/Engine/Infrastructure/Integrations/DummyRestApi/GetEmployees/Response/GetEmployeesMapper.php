<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployees\Response;

use App\Engine\Infrastructure\Integrations\DummyRestApi\Dto\Employee;
use App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployees\Request\GetEmployeesAction;
use IntegrationEngine\Core\Contract\Action\AbstractAction;
use IntegrationEngine\Core\Contract\Mapper\AbstractMapper;
use IntegrationEngine\Core\Contract\Response\ResponseInterface;

final class GetEmployeesMapper extends AbstractMapper
{
    public static function getAction(): string
    {
        return GetEmployeesAction::class;
    }

    protected static function transform(AbstractAction $action, array $response): ResponseInterface
    {
        $employees = array_map(
            static fn (array $data): Employee => Employee::create($data),
            $response['data'] ?? [],
        );

        return GetEmployeesResponse::create($employees);
    }
}

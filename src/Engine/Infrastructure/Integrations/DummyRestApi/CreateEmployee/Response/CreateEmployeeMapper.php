<?php

declare(strict_types=1);

namespace App\Engine\Infrastructure\Integrations\DummyRestApi\CreateEmployee\Response;

use App\Engine\Infrastructure\Integrations\DummyRestApi\CreateEmployee\Request\CreateEmployeeAction;
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
        $data = $response['data'];

        return new CreateEmployeeResponse(
            id:     (int) $data['id'],
            name:   $data['name'],
            salary: (int) $data['salary'],
            age:    (int) $data['age'],
        );
    }
}

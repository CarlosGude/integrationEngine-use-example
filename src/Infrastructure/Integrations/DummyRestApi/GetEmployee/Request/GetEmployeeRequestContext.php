<?php

namespace App\Infrastructure\Integrations\DummyRestApi\GetEmployee\Request;

use IntegrationEngine\Core\Contract\ActionContextInterface;

final readonly class GetEmployeeRequestContext implements ActionContextInterface
{

    public function __construct(private array $data)
    {
    }
    public static function create(array $data): ActionContextInterface
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return $this->data;
    }
}

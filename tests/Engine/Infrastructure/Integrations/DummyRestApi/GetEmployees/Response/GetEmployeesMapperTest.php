<?php

declare(strict_types=1);

namespace App\Tests\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployees\Response;

use App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployees\Request\GetEmployeesAction;
use App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployees\Response\GetEmployeesMapper;
use App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployees\Response\GetEmployeesResponse;
use PHPUnit\Framework\TestCase;

/**
 * Mappers are pure static functions — no engine needed to test them.
 * Call AbstractMapper::map() directly with the raw API response shape.
 */
final class GetEmployeesMapperTest extends TestCase
{
    public function testMapsRawRestResponseToTypedResponse(): void
    {
        $action = GetEmployeesAction::create('GET', '/api/v1/employees');

        $response = GetEmployeesMapper::map($action, [
            'data' => [
                ['id' => 1, 'employee_name' => 'Alice', 'employee_salary' => 5000, 'employee_age' => 30],
                ['id' => 2, 'employee_name' => 'Bob', 'employee_salary' => 6000, 'employee_age' => 25],
            ],
        ]);

        self::assertInstanceOf(GetEmployeesResponse::class, $response);
        self::assertCount(2, $response->employees);
        self::assertSame(1, $response->employees[0]->id);
        self::assertSame('Alice', $response->employees[0]->name);
        self::assertSame(5000, $response->employees[0]->salary);
    }

    public function testHandlesEmptyData(): void
    {
        $action = GetEmployeesAction::create('GET', '/api/v1/employees');

        $response = GetEmployeesMapper::map($action, ['data' => []]);

        self::assertInstanceOf(GetEmployeesResponse::class, $response);
        self::assertCount(0, $response->employees);
    }
}

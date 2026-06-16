<?php

declare(strict_types=1);

namespace App\Tests\Engine\Infrastructure;

use App\Engine\Domain\Employee;
use App\Engine\Infrastructure\DummyRestApiGateway;
use App\Engine\Infrastructure\Integrations\DummyRestApi\Dto\Employee as EmployeeDto;
use App\Engine\Infrastructure\Integrations\DummyRestApi\DummyRestApiIntegration;
use App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployee\Response\GetEmployeeResponse;
use App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployees\Response\GetEmployeesResponse;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Anti-Corruption Layer in isolation.
 * Mock the integration facade → verify domain objects come out the other side.
 */
final class DummyRestApiGatewayTest extends TestCase
{
    public function testFindAllTranslatesIntegrationDtosToDomainObjects(): void
    {
        $integration = $this->createMock(DummyRestApiIntegration::class);
        $integration->method('getEmployees')->willReturn(
            GetEmployeesResponse::create([
                EmployeeDto::create(['id' => 1, 'employee_name' => 'Alice', 'employee_salary' => 5000, 'employee_age' => 30]),
                EmployeeDto::create(['id' => 2, 'employee_name' => 'Bob', 'employee_salary' => 6000, 'employee_age' => 25]),
            ]),
        );

        $gateway = new DummyRestApiGateway($integration);
        $employees = $gateway->findAll();

        self::assertCount(2, $employees);
        self::assertContainsOnlyInstancesOf(Employee::class, $employees);
        self::assertSame('Alice', $employees[0]->name);
        self::assertSame(5000, $employees[0]->salary);
    }

    public function testFindTranslatesSingleDto(): void
    {
        $integration = $this->createMock(DummyRestApiIntegration::class);
        $integration->method('getEmployee')->with(42)->willReturn(
            GetEmployeeResponse::create(
                EmployeeDto::create(['id' => 42, 'employee_name' => 'Carol', 'employee_salary' => 7000, 'employee_age' => 35]),
            ),
        );

        $gateway = new DummyRestApiGateway($integration);
        $employee = $gateway->find(42);

        self::assertInstanceOf(Employee::class, $employee);
        self::assertSame(42, $employee->id);
        self::assertSame('Carol', $employee->name);
    }
}

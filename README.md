# IntegrationEngine — Demo Project

A working Symfony application demonstrating
[`carlosgude/integration-engine`](https://packagist.org/packages/carlosgude/integration-engine)
against the public [Dummy REST API](https://dummy.restapiexample.com).

---

## What this project shows

Two endpoints, fully wired through the bundle:

| Route | Description |
|-------|-------------|
| `GET /employees` | Fetch all employees |
| `GET /employee/{id}` | Fetch a single employee by ID |

The bundle handles the HTTP transport, path resolution, response mapping, and
Symfony DI wiring. The controllers know nothing about HTTP clients or JSON
parsing — they call a facade and get back typed objects.

---

## Requirements

- PHP 8.4+
- Symfony 8.1
- Composer

---

## Installation

```bash
git clone https://github.com/CarlosGude/develop-integration-engine
cd develop-integration-engine
composer install
```

No database, no environment variables, no Docker required. The demo works
out of the box against a public API.

---

## Usage

Start the Symfony dev server:

```bash
symfony server:start
```

Then hit the endpoints:

```bash
# All employees
curl http://localhost:8000/employees

# Single employee
curl http://localhost:8000/employee/1
```

---

## Project structure

```
src/
├── Controller/
│   ├── GetEmployees.php          ← thin, delegates to the facade
│   └── GetEmployee.php
└── Infrastructure/
    └── Integrations/
        └── DummyRestApi/
            ├── DummyRestApiIntegration.php   ← facade
            ├── DummyRestApi.yaml             ← action config
            ├── Dtos/
            │   └── Employee.php              ← value object
            ├── Collections/
            │   └── EmployeeCollection.php    ← typed collection
            ├── GetEmployees/
            │   ├── Request/GetEmployeesAction.php
            │   └── Response/
            │       ├── GetEmployeesMapper.php
            │       └── GetEmployeesResponse.php
            └── GetEmployee/
                ├── Request/
                │   ├── GetEmployeeAction.php
                │   └── GetEmployeeRequestContext.php
                └── Response/
                    ├── GetEmployeeMapper.php
                    └── GetEmployeeResponse.php
```

---

## How it works

### 1. Bundle configuration

`config/packages/integration_engine.yaml` registers the integration:

```yaml
integration_engine:
  integrations:
    dummy_rest_api:
      base_url: 'https://dummy.restapiexample.com'
      config_path: '%kernel.project_dir%/src/Infrastructure/Integrations/DummyRestApi/DummyRestApi.yaml'
```

### 2. Action configuration

`DummyRestApi.yaml` declares available operations:

```yaml
GetEmployees:
  action: App\Infrastructure\Integrations\DummyRestApi\GetEmployees\Request\GetEmployeesAction
  method: GET
  path: /api/v1/employees

GetEmployee:
  action: App\Infrastructure\Integrations\DummyRestApi\GetEmployee\Request\GetEmployeeAction
  method: GET
  path: /api/v1/employee/{id}
```

### 3. Facade

`DummyRestApiIntegration` wraps the engine and exposes named methods:

```php
public function getEmployee(int $id): GetEmployeeResponse
{
    return $this->integrationEngine->send(
        GetEmployeeAction::getName(),
        GetEmployeeRequestContext::create(['id' => $id])
    );
}
```

### 4. Mapper

`GetEmployeeMapper` transforms the raw API response into a typed object:

```php
protected static function transform(AbstractAction $action, array $response): ResponseInterface
{
    return new GetEmployeeResponse(
        Employee::create(
            id: (int) $response['data']['id'],
            name: $response['data']['employee_name'],
            age: (int) $response['data']['employee_age'],
        )
    );
}
```

### 5. Controller

```php
#[Route('/employee/{id}')]
public function index(int $id): JsonResponse
{
    return new JsonResponse(
        $this->serializer->normalize(
            $this->dummyRestApiIntegration->getEmployee($id)->toArray()
        )
    );
}
```

The controller imports nothing from `IntegrationEngine\`. The bundle is
invisible above the integration layer.

---

## Generating the scaffolding

The integration skeleton was generated with:

```bash
php bin/console make:integration DummyRestApi GetEmployees
# > Base URL: https://dummy.restapiexample.com
# > Path: /api/v1/employees
# > Method: GET

php bin/console make:integration DummyRestApi GetEmployee
# > Path: /api/v1/employee/{id}
# > Method: GET
```

The mappers, DTOs, collection and facade were written by hand on top of the
generated skeleton.

---

## Bundle

This project uses [`carlosgude/integration-engine`](https://packagist.org/packages/carlosgude/integration-engine).

Full bundle documentation: [DOCUMENTATION.md](https://github.com/CarlosGude/integrationEngine/blob/main/DOCUMENTATION.md)
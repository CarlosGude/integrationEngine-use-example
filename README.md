# IntegrationEngine — Comparison Project

This project implements the **same 4 endpoints twice** against the public
[Dummy REST API](https://dummy.restapiexample.com): once the traditional way and once
using [`carlosgude/integration-engine`](https://packagist.org/packages/carlosgude/integration-engine).

Every route under `/traditional/*` has a mirror under `/engine/*` that returns the
same JSON. The only difference is how each side gets there.

---

## Routes

| Method | Traditional | Engine | Description |
|--------|------------|--------|-------------|
| `GET`  | `/traditional/employees` | `/engine/employees` | List all employees |
| `GET`  | `/traditional/employees/{id}` | `/engine/employees/{id}` | Single employee |
| `POST` | `/traditional/employees` | `/engine/employees` | Create an employee |
| `GET`  | `/traditional/employees/batch?ids=1,2,3` | `/engine/employees/batch?ids=1,2,3` | Parallel batch |

---

## Setup

```bash
composer install
symfony server:start
```

No database, no Docker, no environment variables.

---

## Side-by-side: what changes

### GET — list all employees

**Traditional** (`src/Traditional/DummyRestApiClient.php`)
```php
public function getEmployees(): array
{
    $response = $this->httpClient->request('GET', self::BASE_URL . '/api/v1/employees');
    return $response->toArray()['data'];
}
```
The controller must map API field names (`employee_name`, `employee_salary`, …) manually.
It knows the API schema. If the API renames a field, you search for every place that
touches the raw array.

**Engine** (`src/Engine/Infrastructure/Integrations/DummyRestApi/DummyRestApi.yaml`)
```yaml
GetEmployees:
    action: App\Engine\Infrastructure\Integrations\DummyRestApi\GetEmployees\Request\GetEmployeesAction
    method: GET
    path: /api/v1/employees
```
The endpoint is declared once. The mapper (`GetEmployeesMapper`) is the only place that
knows the API field names. The controller receives a typed `GetEmployeesResponse` and
never sees a raw array.

---

### GET — single employee with path parameter

**Traditional**
```php
public function getEmployee(int $id): array
{
    $response = $this->httpClient->request('GET', self::BASE_URL . '/api/v1/employee/' . $id);
    return $response->toArray()['data'];
}
```
Path is built by string concatenation. No validation.

**Engine**
```yaml
GetEmployee:
    action: ...GetEmployeeAction
    method: GET
    path: /api/v1/employee/{id}
```
```php
$this->engine->send(
    actionName: GetEmployeeAction::getName(),
    context: DefaultActionContext::create(['id' => $id]),
);
```
`{id}` is resolved from the context. The engine throws if a required placeholder is missing.

---

### POST — request body

**Traditional**
```php
$this->httpClient->request('POST', self::BASE_URL . '/api/v1/create', [
    'json' => ['name' => $name, 'salary' => (string) $salary, 'age' => (string) $age],
]);
```
Body is assembled inline. If the body shape is reused across callers, it is duplicated.

**Engine** (`src/Engine/Infrastructure/Integrations/DummyRestApi/CreateEmployee/Request/CreateEmployeeBody.php`)
```php
final readonly class CreateEmployeeBody implements ActionBodyInterface
{
    public static function create(array $data): self { ... }
    public function toArray(): array
    {
        return ['name' => $this->name, 'salary' => $this->salary, 'age' => $this->age];
    }
}
```
The body is a typed value object. The engine serializes it. Every caller uses the same
contract — no inline arrays, no typos in field names.

---

### Batch — N employees at once

**Traditional** (`src/Traditional/DummyRestApiClient.php`)
```php
// Sequential — each request waits for the previous one.
// Total time = sum of all individual request times.
foreach ($ids as $id) {
    $employees[$id] = $this->getEmployee($id);
}
```

**Engine** (`src/Engine/Infrastructure/Integrations/DummyRestApi/DummyRestApiIntegration.php`)
```php
// Concurrent — all requests are dispatched simultaneously.
// Total time ≈ the slowest single request, not the sum of all.
return $this->engine->sendManyOrFail($requests);
```
The default REST client implements `BatchClientInterface`, so `sendManyOrFail()` gets
real HTTP concurrency for free — no configuration needed.

---

### Anti-Corruption Layer

**Traditional** — controllers call the client and map raw arrays directly. They know
the API field names. If the API changes, you update the client AND every controller that
touched the response.

**Engine** — `EmployeeService` (`src/Engine/Application/EmployeeService.php`) is the
only class that translates the integration DTO into a domain object:

```
Controller → EmployeeService → DummyRestApiIntegration → Engine
                 ↓
            Domain\Employee  ←  (translation happens only here)
```

Controllers import `App\Engine\Domain\Employee`. They know nothing about the API,
about `employee_name`, or about the integration layer. If the API changes a field,
only the mapper and `toDomain()` in `EmployeeService` need to change.

---

## Project structure

```
src/
├── Traditional/                             ← plain HttpClient, no framework
│   ├── DummyRestApiClient.php               ← manual URL building, raw arrays, sequential batch
│   └── Controller/
│       ├── GetEmployeesController.php
│       ├── GetEmployeeController.php
│       ├── CreateEmployeeController.php
│       └── GetManyEmployeesController.php   ← sequential foreach
│
└── Engine/                                  ← Integration Engine
    ├── Application/
    │   └── EmployeeService.php              ← Anti-Corruption Layer
    ├── Controller/
    │   ├── GetEmployeesController.php
    │   ├── GetEmployeeController.php
    │   ├── CreateEmployeeController.php
    │   └── GetManyEmployeesController.php   ← concurrent sendManyOrFail()
    ├── Domain/
    │   └── Employee.php                     ← no engine imports, pure domain
    └── Infrastructure/
        └── Integrations/
            └── DummyRestApi/
                ├── DummyRestApiIntegration.php   ← facade: typed methods, no raw arrays
                ├── DummyRestApi.yaml             ← all endpoints declared here, one place
                ├── Dtos/Employee.php
                ├── GetEmployees/{Request,Response}/
                ├── GetEmployee/{Request,Response}/
                └── CreateEmployee/{Request,Response}/
```

---

## What you gain with the engine

| | Traditional | Integration Engine |
|--|-------------|-------------------|
| Endpoint declaration | Scattered in service methods | One YAML file |
| Path building | String concatenation | `{placeholder}` templates, validated |
| Request body | Inline array | Typed `ActionBodyInterface` |
| Response mapping | Ad-hoc, per-caller | One mapper per action, enforced by the engine |
| Type safety | Raw `array<string, mixed>` | Typed `ResponseInterface` objects |
| Batch | Sequential `foreach` | Concurrent `sendManyOrFail()` |
| Anti-Corruption Layer | Not enforced | `EmployeeService` is the only bridge |
| Adding a new endpoint | Add method, build URL, parse response | Add Action + Mapper + Response + 1 YAML line |
| Auth (bearer, basic, api\_key, OAuth 2.0) | Manual header injection | Declared in YAML, engine handles it |

---

## Bundle

[`carlosgude/integration-engine`](https://packagist.org/packages/carlosgude/integration-engine)
— full documentation: [DOCUMENTATION.md](https://github.com/CarlosGude/integrationEngine/blob/main/DOCUMENTATION.md)

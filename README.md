# Integration Engine — Code Reference

> **This is not a working application.** It is a code reference — read it, not run it.
>
> The goal: after reading the [documentation](https://integrationengine.dev), arrive here and see exactly what the pattern looks like in a real Symfony project across three different protocols. The endpoints exist so you can hit them if you want, but correctness of the responses is not the point.

The engine is **protocol-agnostic**. Whether the external system speaks REST, GraphQL, SOAP, or returns a CSV file, the pattern is identical: Action → Mapper → Response → Integration → Gateway. The three integrations here each exercise a different part of the engine's API, but the structure never changes.

Uses [`carlosgude/integration-engine`](https://packagist.org/packages/carlosgude/integration-engine).

---

## What it demonstrates

| Integration | Protocol | Auth | Engine features |
|---|---|---|---|
| [DummyRestApi](#dummyrestapi) | REST | None | `send()`, `sendManyOrFail()`, `sendMany()` + `BatchResultCollection`, `ActionBodyInterface`, `EmptyResponse` |
| [Rick & Morty](#rick--morty-graphql) | GraphQL | None | `GraphQLBodyInterface`, `client: graphql`, concurrent GraphQL batch |
| [Spotify](#spotify) | REST | Dynamic OAuth2 | `type: dynamic` auth, heterogeneous `sendMany()` |
| [Posts](#posts-dynamic-base-url) | REST | None | `send(baseUrl: ...)` — base URL resolved per request, not at compile time |

All four follow the same structure. The "what" changes; the pattern does not.

---

## The pattern

```
integration_engine.yaml          ← registers the integration (base_url, config_path, client)
       │
       ▼
{Integration}.yaml               ← declares each endpoint (action, method, path, auth, body)
       │
       ▼
Action → Mapper → Response       ← one set per endpoint; the engine's building blocks
       │
       ▼
{Name}Integration.php            ← typed facade; no raw arrays leave this class
       │
       ▼
{Name}Gateway.php                ← Anti-Corruption Layer: integration DTOs → domain objects
       │
       ▼
Ports/{Name}/…Controller.php     ← HTTP entry point; depends on domain only
```

---

## Setup

```bash
composer install
symfony server:start
```

No database, no Docker. For Spotify, add credentials to `.env`:

To scaffold a new endpoint in your own project:

```bash
php bin/console make:integration MyApi GetOrder
```

Generates Action, Mapper, and Response with the correct structure — only `transform()` and the DTO fields left to fill in.

```
SPOTIFY_CREDENTIALS=base64(client_id:client_secret)
```

---

## DummyRestApi

REST API, no authentication. The most complete integration — covers the full engine API surface.

### `send()` with a context (path parameter)

```yaml
# DummyRestApi.yaml
GetEmployee:
    action: ...GetEmployeeAction
    method: GET
    path: /api/v1/employee/{id}    ← {id} resolved from context at call time
```

```php
// DummyRestApiIntegration.php
$this->engine->send(
    actionName: GetEmployeeAction::getName(),
    context: DefaultActionContext::create(['id' => $id]),
);
```

### `send()` with a body (POST)

YAML must declare the body class — the engine uses it to deserialize at runtime:

```yaml
CreateEmployee:
    action: ...CreateEmployeeAction
    method: POST
    path: /api/v1/create
    body: ...CreateEmployeeBody    ← required when body is passed at runtime
```

```php
// CreateEmployeeBody.php
final readonly class CreateEmployeeBody implements ActionBodyInterface
{
    public function toArray(): array
    {
        return ['name' => $this->name, 'salary' => $this->salary, 'age' => $this->age];
    }
}
```

### `hasResponse(): false` → EmptyResponse (DELETE)

When there is no response body, return `false` and `null`. The engine returns `EmptyResponse` automatically:

```php
// DeleteEmployeeAction.php
public static function hasResponse(): bool { return false; }
public static function mapper(): ?string   { return null; }
```

### `sendManyOrFail()` — concurrent fail-fast batch

All requests are dispatched simultaneously. Total time ≈ the slowest single request, not the sum. Throws if any request fails:

```php
// DummyRestApiIntegration.php
foreach ($ids as $id) {
    $requests[$id] = EngineRequest::create(
        actionName: GetEmployeeAction::getName(),
        context: DefaultActionContext::create(['id' => $id]),
    );
}
return $this->engine->sendManyOrFail($requests);
```

### `sendMany()` — concurrent tolerant batch

Individual failures do not abort the rest. Returns a `BatchResultCollection`:

```php
$results = $this->engine->sendMany($requests);
$results->responses();  // successful results, keyed by input key
$results->errors();     // failed results, same keys
$results->hasFailures();
```

### Endpoints

| Method | URL | Description |
|---|---|---|
| `GET` | `/engine/employees` | All employees |
| `GET` | `/engine/employees/{id}` | Single employee |
| `POST` | `/engine/employees` | Create — body: `{"name":"…","salary":0,"age":0}` |
| `DELETE` | `/engine/employees/{id}` | Delete (EmptyResponse → 204) |
| `GET` | `/engine/employees/batch?ids=1,2,3` | Concurrent batch, fail-fast |
| `GET` | `/engine/employees/batch-resilient?ids=1,2,3` | Concurrent batch, tolerant |

### Key files

```
Infrastructure/Integrations/DummyRestApi/
├── DummyRestApi.yaml
├── DummyRestApiIntegration.php
├── Dto/Employee.php
├── GetEmployee/{Request,Response}/
├── GetEmployees/{Request,Response}/
├── CreateEmployee/{Request,Response}/    ← ActionBodyInterface
└── DeleteEmployee/Request/               ← hasResponse: false, no mapper
Infrastructure/DummyRestApiGateway.php    ← ACL: integration DTO → Domain\Employee
```

---

## Rick & Morty (GraphQL)

Public GraphQL API, no authentication. Shows how the engine handles GraphQL natively with zero boilerplate.

### `GraphQLBodyInterface` — typed query + variables

```php
// GetCharacterBody.php
final readonly class GetCharacterBody implements GraphQLBodyInterface
{
    public function getQuery(): string
    {
        return <<<'GQL'
            query GetCharacter($id: ID!) {
                character(id: $id) { id name status species gender image
                    origin { name } location { name }
                }
            }
            GQL;
    }

    public function getVariables(): array { return ['id' => (string) $this->id]; }
}
```

### `client: graphql` — no method or path needed

```yaml
# integration_engine.yaml
rick_and_morty:
    base_url: 'https://rickandmortyapi.com/graphql'
    client: graphql                  ← switches to GraphQLClientAdapter

# RickAndMorty.yaml
GetCharacter:
    action: ...GetCharacterAction
    body: ...GetCharacterBody        ← no method, no path — GraphQL always POSTs to base_url
```

The `GraphQLClientAdapter` unwraps `data.data` automatically — mappers receive the inner payload:

```php
// GetCharacterMapper.php
// $response is already ['character' => {...}], not ['data' => ['character' => {...}]]
return GetCharacterResponse::create(Character::create($response['character'] ?? []));
```

### Batch with `sendManyOrFail()`

Each item gets its own `EngineRequest` with its own body — the same pattern as DummyRestApi:

```php
foreach ($ids as $id) {
    $requests[$id] = EngineRequest::create(
        actionName: GetCharacterAction::getName(),
        body: GetCharacterBody::create(['id' => $id]),
    );
}
return $this->engine->sendManyOrFail($requests);
```

> **Note:** The built-in `client: graphql` adapter does not implement `BatchClientInterface` — requests run sequentially. For real concurrency with GraphQL, use `client_service:` with a client that implements `BatchClientInterface`. The pattern here is identical to the REST batch; only the client changes.

### Endpoints

| Method | URL | Description |
|---|---|---|
| `GET` | `/engine/characters/{id}` | Single character via GraphQL |
| `GET` | `/engine/characters/batch?ids=1,2,3` | Batch via GraphQL |

### Key files

```
Infrastructure/Integrations/RickAndMorty/
├── RickAndMorty.yaml
├── RickAndMortyIntegration.php
├── Dto/Character.php
└── GetCharacter/{Request,Response}/    ← GetCharacterBody implements GraphQLBodyInterface
Infrastructure/RickAndMortyGateway.php  ← ACL: integration DTO → Domain\Character
```

---

## Spotify

REST API with dynamic OAuth2. Shows how the engine handles token lifecycle automatically.

### `type: dynamic` auth — fetch, cache, inject, refresh

```yaml
# Spotify.yaml
FetchToken:
    action: ...FetchTokenAction
    method: POST
    path: /api/token
    authorization:
        type: basic
        token: '%env(SPOTIFY_CREDENTIALS)%'   ← Basic auth for the token request

GetArtist:
    action: ...GetArtistAction
    method: GET
    path: /v1/artists/{id}
    authorization:
        type: dynamic
        action: FetchToken             ← engine calls FetchToken automatically
        token_field: access_token      ← field to extract from FetchTokenResponse
        ttl: 3600                      ← cached; FetchToken only called when expired
```

The engine handles the full token lifecycle: fetch on first call, inject as `Authorization: Bearer`, cache for `ttl` seconds, retry on 401. Zero token management code in the application.

> **Demo note:** Spotify's token endpoint requires `application/x-www-form-urlencoded` but the engine sends JSON by default — see `FetchTokenAction` for details and the production fix path.

### Heterogeneous `sendMany()` — two different actions in one batch

Artist profile + top tracks dispatched simultaneously, both using the same cached token:

```php
// SpotifyIntegration.php
$results = $this->engine->sendMany([
    'artist'     => EngineRequest::create(GetArtistAction::getName(), $context),
    'top_tracks' => EngineRequest::create(GetArtistTopTracksAction::getName(), $context),
]);
```

### Endpoints

| Method | URL | Description |
|---|---|---|
| `GET` | `/engine/artists/{id}` | Artist profile + top tracks (two parallel requests) |

Example: `/engine/artists/4Z8W4fkeB5ccHVFTu6OM`

### Key files

```
Infrastructure/Integrations/Spotify/
├── Spotify.yaml                             ← basic + dynamic auth config
├── SpotifyIntegration.php                   ← heterogeneous sendMany()
├── ArtistProfileResult.php                  ← internal DTO combining both responses
├── Dto/{Artist,Track}.php
├── FetchToken/{Request,Response}/           ← token action; see FetchTokenAction for notes
├── GetArtist/{Request,Response}/
└── GetArtistTopTracks/{Request,Response}/
Infrastructure/SpotifyGateway.php            ← ACL: ArtistProfileResult → Domain\ArtistProfile
```

---

## Posts (dynamic base URL)

REST API, no authentication. Shows `IntegrationEngine::send(baseUrl: ...)`: the engine's
only built-in extension point for an integration that does **not** have one fixed host.

### One configured `base_url`, overridden per request

```yaml
# integration_engine.yaml
posts:
    base_url: 'https://jsonplaceholder.typicode.com'   ← default — used when no $host is passed
    config_path: '%kernel.project_dir%/src/Engine/Infrastructure/Integrations/Posts/Posts.yaml'
```

```php
// PostsIntegration.php
public function getPost(int $id, ?string $host = null): GetPostResponse
{
    $response = $this->engine->send(
        actionName: GetPostAction::getName(),
        context: DefaultActionContext::create(['id' => $id]),
        baseUrl: $host,    ← null keeps the configured base_url; any other value overrides it
    );

    \assert($response instanceof GetPostResponse);

    return $response;
}
```

The same `Action`/`Mapper`/`Response` and YAML config serve every host — `baseUrl` only
changes which server the configured path is sent to. Nothing is persisted, nothing is
recompiled: it's resolved once, for that one call. The integration has no idea what the
`$host` value represents (tenant domain, regional mirror, ...) — that decision belongs to
the caller, never to the bundle.

### Endpoints

| Method | URL | Description |
|---|---|---|
| `GET` | `/engine/posts/{id}` | Uses the configured `base_url` (jsonplaceholder.typicode.com) |
| `GET` | `/engine/posts/{id}?host=https://my-json-server.typicode.com/typicode/demo` | Same action, different host, for this request only |

### Key files

```
Infrastructure/Integrations/Posts/
├── Posts.yaml
├── PostsIntegration.php          ← forwards $host to send(baseUrl: ...)
├── Dto/Post.php
└── GetPost/{Request,Response}/
Infrastructure/PostsGateway.php   ← ACL: integration DTO → Domain\Post
```

---

## Testing

```bash
php bin/phpunit
```

**Mappers** are pure static functions — no engine, no HTTP, no mocks needed:

```php
$action = GetEmployeesAction::create('GET', '/api/v1/employees');

$response = GetEmployeesMapper::map($action, [
    'data' => [
        ['id' => 1, 'employee_name' => 'Alice', 'employee_salary' => 5000, 'employee_age' => 30],
    ],
]);

self::assertInstanceOf(GetEmployeesResponse::class, $response);
self::assertSame('Alice', $response->employees[0]->name);
```

For GraphQL, the test input mirrors what the mapper actually receives — `GraphQLClientAdapter` already unwrapped `data.data` before calling the mapper:

```php
$action = GetCharacterAction::create('', '');

$response = GetCharacterMapper::map($action, [
    'character' => ['id' => 1, 'name' => 'Rick Sanchez', 'status' => 'Alive', ...],
]);
```

**Gateways** (the ACL) are tested by mocking the Integration facade:

```php
$integration = $this->createMock(DummyRestApiIntegration::class);
$integration->method('getEmployees')->willReturn(GetEmployeesResponse::create([...]));

$gateway = new DummyRestApiGateway($integration);
$employees = $gateway->findAll();

self::assertContainsOnlyInstancesOf(Employee::class, $employees);
```

See `tests/` for the full examples.

---

## Architecture

```
src/Engine/
├── Domain/
│   ├── Employee.php
│   ├── Character.php
│   ├── ArtistProfile.php
│   └── Post.php
│
├── Application/
│   └── Ports/
│       ├── DummyRestApi/    ← HTTP entry points: GetEmployee, CreateEmployee, DeleteEmployee…
│       ├── RickAndMorty/    ← HTTP entry points: GetCharacter, GetManyCharacters
│       ├── Spotify/         ← HTTP entry points: GetArtistProfile
│       └── Posts/           ← HTTP entry points: GetPost (?host= overrides base_url)
│
└── Infrastructure/
    ├── DummyRestApiGateway.php    ← ACL
    ├── RickAndMortyGateway.php    ← ACL
    ├── SpotifyGateway.php         ← ACL
    ├── PostsGateway.php           ← ACL
    └── Integrations/
        ├── DummyRestApi/
        ├── RickAndMorty/
        ├── Spotify/
        └── Posts/
```

**Ports** depend only on domain objects. **Gateways** are the only classes that know both integration DTOs and domain objects — the Anti-Corruption Layer. **Integrations** are self-contained: no domain imports, no application logic.

Without this boundary, a breaking change in the external API propagates directly into your domain model. The Gateway is the firewall: the one place where both worlds touch, so adapting to an API change means updating one class.

---

## Bundle

[`carlosgude/integration-engine`](https://packagist.org/packages/carlosgude/integration-engine)
— documentation: [integrationengine.dev](https://integrationengine.dev)

<?php

declare(strict_types=1);

namespace App\Traditional;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Traditional HTTP client wrapper — the common approach without a framework.
 *
 * Problems that accumulate as the integration grows:
 *   - Base URL is scattered or must be injected and repeated everywhere.
 *   - Paths are built by string concatenation — no validation, no reuse.
 *   - Responses are raw arrays; callers must know the exact API field names.
 *   - Batch is sequential: N requests block one after another.
 *   - No enforced contract — anyone can call this and use any field freely.
 */
final class DummyRestApiClient
{
    private const BASE_URL = 'https://dummy.restapiexample.com';

    public function __construct(private readonly HttpClientInterface $httpClient) {}

    /** @return array<int, array<string, mixed>> */
    public function getEmployees(): array
    {
        $response = $this->httpClient->request('GET', self::BASE_URL . '/api/v1/employees');

        return $response->toArray()['data'];
    }

    /** @return array<string, mixed> */
    public function getEmployee(int $id): array
    {
        $response = $this->httpClient->request('GET', self::BASE_URL . '/api/v1/employee/' . $id);

        return $response->toArray()['data'];
    }

    /** @return array<string, mixed> */
    public function createEmployee(string $name, int $salary, int $age): array
    {
        $response = $this->httpClient->request('POST', self::BASE_URL . '/api/v1/create', [
            'json' => [
                'name'   => $name,
                'salary' => (string) $salary,
                'age'    => (string) $age,
            ],
        ]);

        return $response->toArray()['data'];
    }

    /**
     * Sequential batch — each request waits for the previous one.
     * Total time = sum of all individual request times.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getManyEmployees(int ...$ids): array
    {
        $employees = [];

        foreach ($ids as $id) {
            $employees[$id] = $this->getEmployee($id);
        }

        return $employees;
    }
}

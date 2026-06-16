<?php

declare(strict_types=1);

namespace App\Engine\Application\Ports\RickAndMorty;

use App\Engine\Domain\Character;
use App\Engine\Infrastructure\RickAndMortyGateway;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class GetManyCharactersController extends AbstractController
{
    public function __construct(private readonly RickAndMortyGateway $gateway) {}

    /**
     * Concurrent GraphQL batch — each ID triggers its own query, all dispatched in parallel.
     * Usage: GET /engine/characters/batch?ids=1,2,3.
     */
    #[Route('/engine/characters/batch', name: 'engine_characters_batch', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $ids = array_values(array_filter(
            array_map('intval', explode(',', $request->query->getString('ids'))),
        ));

        if ([] === $ids) {
            return new JsonResponse([]);
        }

        return new JsonResponse(
            array_map(static fn (Character $c) => $c->toArray(), $this->gateway->getManyCharacters(...$ids)),
        );
    }
}

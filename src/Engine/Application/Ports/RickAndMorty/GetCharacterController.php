<?php

declare(strict_types=1);

namespace App\Engine\Application\Ports\RickAndMorty;

use App\Engine\Infrastructure\RickAndMortyGateway;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class GetCharacterController extends AbstractController
{
    public function __construct(private readonly RickAndMortyGateway $gateway) {}

    #[Route('/engine/characters/{id}', name: 'engine_character_get', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function __invoke(int $id): JsonResponse
    {
        return new JsonResponse($this->gateway->getCharacter($id)->toArray());
    }
}

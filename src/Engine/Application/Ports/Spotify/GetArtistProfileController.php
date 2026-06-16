<?php

declare(strict_types=1);

namespace App\Engine\Application\Ports\Spotify;

use App\Engine\Infrastructure\SpotifyGateway;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class GetArtistProfileController extends AbstractController
{
    public function __construct(private readonly SpotifyGateway $gateway) {}

    /**
     * Two concurrent Spotify requests combined into one response.
     * Usage: GET /engine/artists/{id}.
     *
     * Example: GET /engine/artists/4Z8W4fkeB5ccHVFTu6OM  (The Beatles)
     */
    #[Route('/engine/artists/{id}', name: 'engine_artist_profile', methods: ['GET'])]
    public function __invoke(string $id): JsonResponse
    {
        return new JsonResponse(
            $this->gateway->getArtistProfile($id)->toArray(),
        );
    }
}

<?php

namespace App\Controller;



use App\Infrastructure\Integrations\DummyRestApi\DummyRestApiIntegration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
final class GetEmployee extends AbstractController
{
    public function __construct(
        protected DummyRestApiIntegration $dummyRestApiIntegration,
        protected SerializerInterface $serializer
    )
    {

    }

    #[Route('/employee/{id}', name: 'employee')]
    public function index(int $id): JsonResponse
    {
        $response = $this->dummyRestApiIntegration
            ->getEmployee($id)
            ->toArray();

        return new JsonResponse(
            $this->serializer->normalize($response)
        );
    }
}

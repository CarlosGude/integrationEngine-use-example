<?php

namespace App\Controller;



use App\Infrastructure\Integrations\DummyRestApi\DummyRestApiIntegration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
final class GetEmployees extends AbstractController
{
    public function __construct(
        protected DummyRestApiIntegration $dummyRestApiIntegration,
        protected SerializerInterface $serializer
    )
    {

    }

    #[Route('/employees', name: 'employees')]
    public function index(): JsonResponse
    {
        $response = $this->dummyRestApiIntegration
            ->getEmployees()
            ->toArray();

        return new JsonResponse(
            $this->serializer->normalize($response)
        );
    }
}

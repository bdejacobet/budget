<?php

declare(strict_types=1);

namespace App\EnvelopeManagement\UI\Http\Rest\Envelope\Controller;

use App\EnvelopeManagement\Application\Envelope\Query\ShowEnvelopeQuery;
use App\EnvelopeManagement\Domain\Shared\Adapter\QueryBusInterface;
use App\EnvelopeManagement\UI\Http\Rest\Envelope\Exception\ShowEnvelopeControllerException;
use App\SharedContext\Domain\SharedUserInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/envelope/{uuid}', name: 'app_envelope_show', methods: ['GET'])]
#[IsGranted('ROLE_USER')]
class ShowEnvelopeController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    public function __invoke(
        string $uuid,
        #[CurrentUser] SharedUserInterface $user
    ): JsonResponse {
        try {
            $envelope = $this->queryBus->query(new ShowEnvelopeQuery($uuid, $user->getUuid()));
        } catch (\Throwable $exception) {
            $this->logger->error('Failed to process Envelope show request: '.$exception->getMessage());

            throw new ShowEnvelopeControllerException(ShowEnvelopeControllerException::MESSAGE, $exception->getCode(), $exception);
        }

        return $this->json($envelope, Response::HTTP_OK);
    }
}

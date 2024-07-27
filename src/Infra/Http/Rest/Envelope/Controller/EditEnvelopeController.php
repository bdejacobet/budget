<?php

declare(strict_types=1);

namespace App\Infra\Http\Rest\Envelope\Controller;

use App\Application\Envelope\Command\UpdateEnvelopeCommand;
use App\Application\Envelope\Query\GetOneEnvelopeQuery;
use App\Domain\Envelope\Dto\UpdateEnvelopeDto;
use App\Domain\Envelope\Entity\Envelope;
use App\Domain\Envelope\Entity\EnvelopeInterface;
use App\Domain\Shared\Adapter\MessengerCommandBusInterface;
use App\Domain\Shared\Adapter\MessengerQueryBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/envelope/{id}/edit', name: 'app_envelope_edit', methods: ['PUT'])]
class EditEnvelopeController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly MessengerCommandBusInterface $commandBus,
        private readonly MessengerQueryBusInterface $queryBus,
    ) {
    }

    public function __invoke(
        #[MapRequestPayload] UpdateEnvelopeDto $updateEnvelopeDto,
        Envelope $envelope,
    ): JsonResponse {
        try {
            $parentEnvelope = $updateEnvelopeDto->getParentId() ? $this->queryBus->query(
                new GetOneEnvelopeQuery($updateEnvelopeDto->getParentId())
            ) : null;
            $this->commandBus->execute(
                new UpdateEnvelopeCommand(
                    $envelope,
                    $updateEnvelopeDto,
                    $parentEnvelope instanceof EnvelopeInterface ? $parentEnvelope : null
                )
            );
        } catch (\Throwable $exception) {
            $this->logger->error('Failed to process Envelope update request: '.$exception->getMessage());

            return $this->json(['error' => $exception->getMessage()], $exception->getCode());
        }

        return $this->json(['message' => 'Envelope update request received'], Response::HTTP_ACCEPTED);
    }
}

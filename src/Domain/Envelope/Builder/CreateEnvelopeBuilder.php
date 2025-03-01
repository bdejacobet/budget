<?php

declare(strict_types=1);

namespace App\Domain\Envelope\Builder;

use App\Application\Envelope\Dto\CreateEnvelopeInputInterface;
use App\Domain\Envelope\Exception\Builder\CreateEnvelopeBuilderException;
use App\Domain\Envelope\Model\EnvelopeInterface;
use App\Domain\Envelope\Validator\CreateEnvelopeCurrentBudgetValidator;
use App\Domain\Envelope\Validator\CreateEnvelopeTargetBudgetValidator;
use App\Domain\Envelope\Validator\CreateEnvelopeTitleValidator;
use App\Domain\Shared\Adapter\LoggerInterface;
use App\Domain\Shared\Model\UserInterface;

class CreateEnvelopeBuilder implements CreateEnvelopeBuilderInterface
{
    private ?EnvelopeInterface $parentEnvelope = null;
    private CreateEnvelopeInputInterface $createEnvelopeDto;
    private UserInterface $user;

    public function __construct(
        private readonly CreateEnvelopeTargetBudgetValidator $targetBudgetValidator,
        private readonly CreateEnvelopeCurrentBudgetValidator $currentBudgetValidator,
        private readonly CreateEnvelopeTitleValidator $titleValidator,
        private readonly LoggerInterface $logger,
        private readonly string $envelopeClass,
    ) {
        $model = new $envelopeClass();
        if (!$model instanceof EnvelopeInterface) {
            throw new \RuntimeException('Class should be Envelope in CreateEnvelopeBuilder');
        }
    }

    public function setParentEnvelope(?EnvelopeInterface $parentEnvelope): self
    {
        $this->parentEnvelope = $parentEnvelope;

        return $this;
    }

    public function setCreateEnvelopeDto(CreateEnvelopeInputInterface $createEnvelopeDto): self
    {
        $this->createEnvelopeDto = $createEnvelopeDto;

        return $this;
    }

    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @throws CreateEnvelopeBuilderException
     */
    public function build(): EnvelopeInterface
    {
        try {
            $this->titleValidator->validate($this->createEnvelopeDto->getTitle(), $this->user);
            $this->targetBudgetValidator->validate($this->createEnvelopeDto->getTargetBudget(), $this->parentEnvelope);
            $this->currentBudgetValidator->validate($this->createEnvelopeDto->getCurrentBudget(), $this->createEnvelopeDto->getTargetBudget(), $this->parentEnvelope);
            if ($this->parentEnvelope instanceof EnvelopeInterface && 0.00 !== $currentBudget = floatval($this->createEnvelopeDto->getCurrentBudget())) {
                $this->parentEnvelope->updateAncestorsCurrentBudget($currentBudget);
            }

            return (new $this->envelopeClass())
                ->setParent($this->parentEnvelope)
                ->setCurrentBudget($this->createEnvelopeDto->getCurrentBudget())
                ->setTargetBudget($this->createEnvelopeDto->getTargetBudget())
                ->setTitle($this->createEnvelopeDto->getTitle())
                ->setCreatedAt(new \DateTimeImmutable('now'))
                ->setUpdatedAt(new \DateTime('now'))
                ->setUser($this->user);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), [
                'exception' => $exception::class,
                'code'      => $exception->getCode(),
            ]);
            throw new CreateEnvelopeBuilderException(CreateEnvelopeBuilderException::MESSAGE, $exception->getCode(), $exception);
        }
    }
}

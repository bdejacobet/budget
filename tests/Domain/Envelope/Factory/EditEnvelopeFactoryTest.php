<?php

declare(strict_types=1);

namespace App\Tests\Domain\Envelope\Factory;

use App\Application\Envelope\Dto\EditEnvelopeInput;
use App\Domain\Envelope\Builder\EditEnvelopeBuilder;
use App\Domain\Envelope\Entity\EnvelopeCollection;
use App\Domain\Envelope\Exception\ChildrenTargetBudgetsExceedsParentEnvelopeTargetBudgetException;
use App\Domain\Envelope\Exception\EnvelopeCurrentBudgetExceedsEnvelopeTargetBudgetException;
use App\Domain\Envelope\Exception\EnvelopeCurrentBudgetExceedsParentEnvelopeTargetBudgetException;
use App\Domain\Envelope\Exception\EnvelopeTitleAlreadyExistsForUserException;
use App\Domain\Envelope\Exception\SelfParentEnvelopeException;
use App\Domain\Envelope\Factory\EditEnvelopeFactory;
use App\Domain\Envelope\Validator\EditEnvelopeCurrentBudgetValidator;
use App\Domain\Envelope\Validator\EditEnvelopeTargetBudgetValidator;
use App\Domain\Envelope\Validator\EditEnvelopeTitleValidator;
use App\Domain\Shared\Adapter\LoggerInterface;
use App\Infra\Http\Rest\Envelope\Entity\Envelope;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EditEnvelopeFactoryTest extends TestCase
{
    private LoggerInterface&MockObject $logger;
    private EditEnvelopeBuilder $editEnvelopeBuilder;
    private EditEnvelopeFactory $editEnvelopeFactory;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $targetBudgetValidator = new EditEnvelopeTargetBudgetValidator();
        $currentBudgetValidator = new EditEnvelopeCurrentBudgetValidator();
        $this->editEnvelopeBuilder = new EditEnvelopeBuilder($targetBudgetValidator, $currentBudgetValidator, $this->createMock(EditEnvelopeTitleValidator::class));
        $this->editEnvelopeFactory = new EditEnvelopeFactory(
            $this->logger,
            $this->editEnvelopeBuilder
        );
    }

    /**
     * @throws EnvelopeCurrentBudgetExceedsParentEnvelopeTargetBudgetException
     * @throws ChildrenTargetBudgetsExceedsParentEnvelopeTargetBudgetException
     * @throws EnvelopeTitleAlreadyExistsForUserException
     * @throws EnvelopeCurrentBudgetExceedsParentEnvelopeTargetBudgetException
     * @throws SelfParentEnvelopeException
     */
    public function testCreateFromDtoSuccess(): void
    {
        $editEnvelopeDto = new EditEnvelopeInput('Test Title', '50.00', '100.00');
        $parentEnvelope = new Envelope();
        $parentEnvelope->setTargetBudget('200.00');
        $parentEnvelope->setChildren(new EnvelopeCollection());
        $parentEnvelope->setId(1);
        $envelope = new Envelope();
        $envelope->setParent($parentEnvelope);
        $envelope->setId(2);

        $result = $this->editEnvelopeFactory->createFromDto($envelope, $editEnvelopeDto, $parentEnvelope);

        $this->assertInstanceOf(Envelope::class, $result);
    }

    /**
     * @throws EnvelopeCurrentBudgetExceedsParentEnvelopeTargetBudgetException
     * @throws ChildrenTargetBudgetsExceedsParentEnvelopeTargetBudgetException
     * @throws EnvelopeTitleAlreadyExistsForUserException
     * @throws EnvelopeCurrentBudgetExceedsParentEnvelopeTargetBudgetException
     * @throws SelfParentEnvelopeException
     */
    public function testCreateFromDtoFailureDueToChildrenTargetBudgetsExceedsParent(): void
    {
        $editEnvelopeDto = new EditEnvelopeInput('Test Title', '50.00', '300.00');
        $parentEnvelope = new Envelope();
        $parentEnvelope->setTargetBudget('200.00');
        $parentEnvelope->setChildren(new EnvelopeCollection());
        $parentEnvelope->setId(1);
        $envelope = new Envelope();
        $envelope->setParent($parentEnvelope);
        $envelope->setId(2);

        $this->expectException(ChildrenTargetBudgetsExceedsParentEnvelopeTargetBudgetException::class);

        $this->editEnvelopeFactory->createFromDto($envelope, $editEnvelopeDto, $parentEnvelope);
    }

    /**
     * @throws EnvelopeCurrentBudgetExceedsParentEnvelopeTargetBudgetException
     * @throws ChildrenTargetBudgetsExceedsParentEnvelopeTargetBudgetException
     * @throws EnvelopeTitleAlreadyExistsForUserException
     * @throws EnvelopeCurrentBudgetExceedsParentEnvelopeTargetBudgetException
     * @throws SelfParentEnvelopeException
     */
    public function testCreateFromDtoFailureDueToCurrentBudgetExceedsTarget(): void
    {
        $editEnvelopeDto = new EditEnvelopeInput('Test Title', '250.00', '100.00');
        $parentEnvelope = new Envelope();
        $parentEnvelope->setId(1);
        $parentEnvelope->setTargetBudget('200.00');
        $parentEnvelope->setChildren(new EnvelopeCollection());
        $envelope = new Envelope();
        $envelope->setParent($parentEnvelope);
        $envelope->setId(2);

        $this->expectException(EnvelopeCurrentBudgetExceedsEnvelopeTargetBudgetException::class);

        $this->editEnvelopeFactory->createFromDto($envelope, $editEnvelopeDto, $parentEnvelope);
    }

    /**
     * @throws EnvelopeCurrentBudgetExceedsParentEnvelopeTargetBudgetException
     * @throws ChildrenTargetBudgetsExceedsParentEnvelopeTargetBudgetException
     * @throws EnvelopeTitleAlreadyExistsForUserException
     * @throws EnvelopeCurrentBudgetExceedsParentEnvelopeTargetBudgetException
     * @throws SelfParentEnvelopeException
     */
    public function testHandleParentChange(): void
    {
        $editEnvelopeDto = new EditEnvelopeInput('Test Title', '100.00', '150.00');
        $parentEnvelope = new Envelope();
        $parentEnvelope->setId(1);
        $parentEnvelope->setTargetBudget('200.00');
        $parentEnvelope->setCurrentBudget('150.00');
        $parentEnvelope->setChildren(new EnvelopeCollection());

        $envelope = new Envelope();
        $envelope->setId(2);
        $envelope->setParent($parentEnvelope);
        $envelope->setCurrentBudget('50.00');
        $envelope->setTargetBudget('50.00');

        $newParentEnvelope = new Envelope();
        $newParentEnvelope->setId(3);
        $newParentEnvelope->setTargetBudget('300.00');
        $newParentEnvelope->setCurrentBudget('100.00');
        $newParentEnvelope->setChildren(new EnvelopeCollection());

        $this->editEnvelopeFactory->createFromDto($envelope, $editEnvelopeDto, $newParentEnvelope);

        $this->assertEquals('100.00', $parentEnvelope->getCurrentBudget());
        $this->assertEquals('250.00', $newParentEnvelope->getCurrentBudget());
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Envelope\Builder;

use App\Application\Envelope\Dto\EditEnvelopeInputInterface;
use App\Domain\Envelope\Model\EnvelopeInterface;

interface EditEnvelopeBuilderInterface
{
    public function setEnvelope(EnvelopeInterface $envelope): self;

    public function setUpdateEnvelopeDto(EditEnvelopeInputInterface $updateEnvelopeDto): self;

    public function setParentEnvelope(?EnvelopeInterface $newParentEnvelope): self;

    public function build(): EnvelopeInterface;
}

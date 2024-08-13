<?php

declare(strict_types=1);

namespace App\Domain\Envelope\Entity;

interface EnvelopesPaginatedInterface
{
    public function getEnvelopes(): iterable;

    public function getTotalItems(): int;
}

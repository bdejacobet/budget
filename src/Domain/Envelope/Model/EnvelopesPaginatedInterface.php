<?php

declare(strict_types=1);

namespace App\Domain\Envelope\Model;

interface EnvelopesPaginatedInterface
{
    /**
     * @return iterable<int, EnvelopeInterface>
     */
    public function getEnvelopes(): iterable;

    public function getTotalItems(): int;
}

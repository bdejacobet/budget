<?php

declare(strict_types=1);

namespace App\Domain\Envelope\Dto;

interface ListEnvelopesDtoInterface
{
    /**
     * @return array<string, string>|null
     */
    public function getOrderBy(): ?array;

    public function getLimit(): ?int;

    public function getOffset(): ?int;

    public function getParentId(): ?int;
}

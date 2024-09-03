<?php

namespace App\UserManagement\Infrastructure\User\Adapter;

use App\EnvelopeManagement\Domain\Shared\Adapter\UuidAdapterInterface;
use Symfony\Component\Uid\Uuid;

readonly class UuidAdapter implements UuidAdapterInterface
{
    public function generate(): string
    {
        return Uuid::v4()->toRfc4122();
    }
}

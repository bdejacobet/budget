<?php

declare(strict_types=1);

namespace App\Infra\Http\Rest\Shared\Adapter;

use App\Domain\Shared\Adapter\UrlGeneratorInterface as CustomUrlGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

readonly class UrlGeneratorAdapter implements CustomUrlGeneratorInterface
{
    public function __construct(private SymfonyUrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function generate(string $route, array $parameters = [], int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        return $this->urlGenerator->generate($route, $parameters, $referenceType);
    }
}

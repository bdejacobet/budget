<?php

declare(strict_types=1);

namespace App\Infra\Http\Rest\User\Repository;

use App\Domain\Shared\Adapter\LoggerInterface;
use App\Domain\User\Exception\UserQueryRepositoryException;
use App\Domain\User\Repository\UserQueryRepositoryInterface;
use App\Infra\Http\Rest\User\Entity\User;
use Elastica\Query;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use FOS\ElasticaBundle\Repository;

class UserQueryRepository extends Repository implements UserQueryRepositoryInterface
{
    public function __construct(
        protected PaginatedFinderInterface $finder,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct($finder);
    }

    /**
     * @param array<string, mixed>       $criteria
     * @param array<string, string>|null $orderBy
     *
     * @throws \Throwable
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?User
    {
        $query = new Query();
        $query->setQuery(new Query\Term($criteria));

        try {
            $result = $this->find($query, 1);
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage());
            throw new UserQueryRepositoryException(sprintf('%s on method findOneBy', UserQueryRepositoryException::MESSAGE), $exception->getCode(), $exception);
        }

        $user = reset($result);

        return $user instanceof User ? $user : null;
    }
}

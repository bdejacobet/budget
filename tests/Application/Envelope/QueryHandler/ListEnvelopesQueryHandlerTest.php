<?php

declare(strict_types=1);

namespace App\Tests\Application\Envelope\QueryHandler;

use App\Application\Envelope\Dto\ListEnvelopesInput;
use App\Application\Envelope\Query\ListEnvelopesQuery;
use App\Application\Envelope\QueryHandler\ListEnvelopesQueryHandler;
use App\Domain\Envelope\Model\EnvelopeInterface;
use App\Domain\Envelope\Model\EnvelopesPaginated;
use App\Domain\Envelope\Model\EnvelopesPaginatedInterface;
use App\Domain\Envelope\Repository\EnvelopeQueryRepositoryInterface;
use App\Infra\Http\Rest\Envelope\Entity\Envelope;
use App\Infra\Http\Rest\User\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ListEnvelopesQueryHandlerTest extends TestCase
{
    private MockObject&EnvelopeQueryRepositoryInterface $envelopeQueryRepositoryMock;
    private ListEnvelopesQueryHandler $listEnvelopesQueryHandler;

    protected function setUp(): void
    {
        $this->envelopeQueryRepositoryMock = $this->createMock(EnvelopeQueryRepositoryInterface::class);
        $this->listEnvelopesQueryHandler = new ListEnvelopesQueryHandler(
            $this->envelopeQueryRepositoryMock,
        );
    }

    /**
     * @dataProvider envelopeDataProvider
     *
     * @param array<EnvelopeInterface> $envelopes
     */
    public function testInvoke(ListEnvelopesQuery $query, array $envelopes, EnvelopesPaginatedInterface $envelopesPaginated): void
    {
        $this->envelopeQueryRepositoryMock->expects($this->once())
            ->method('findBy')
            ->with([
                'user' => $query->getUser()->getId(),
                'parent' => null,
            ])
            ->willReturn(new EnvelopesPaginated($envelopes, \count($envelopes)));

        $result = $this->listEnvelopesQueryHandler->__invoke($query);

        $this->assertEquals($envelopesPaginated, $result);
    }

    /**
     * @return array<mixed>
     */
    public function envelopeDataProvider(): array
    {
        $envelope = new Envelope();
        $envelope->setId(1);

        return [
            'success' => [
                new ListEnvelopesQuery((new User())->setId(1), new ListEnvelopesInput()),
                [$envelope],
                new EnvelopesPaginated([$envelope], 1),
            ],
            'failure' => [
                new ListEnvelopesQuery((new User())->setId(2), new ListEnvelopesInput()),
                [],
                new EnvelopesPaginated([], 0),
            ],
        ];
    }
}

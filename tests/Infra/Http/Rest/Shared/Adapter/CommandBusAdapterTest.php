<?php

declare(strict_types=1);

namespace App\Tests\Infra\Http\Rest\Shared\Adapter;

use App\Domain\Shared\Command\CommandInterface;
use App\Infra\Http\Rest\Shared\Adapter\CommandBusAdapter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Envelope;

class CommandBusAdapterTest extends TestCase
{
    /**
     * @throws \Throwable
     */
    public function testExecute(): void
    {
        $messageBus = $this->createMock(MessageBusInterface::class);
        $command = $this->createMock(CommandInterface::class);
        $envelope = new Envelope($command);

        $messageBus->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willReturn($envelope);

        $adapter = new CommandBusAdapter($messageBus);
        $adapter->execute($command);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testExecuteThrowsException(): void
    {
        $this->expectException(\Throwable::class);

        $messageBus = $this->createMock(MessageBusInterface::class);
        $command = $this->createMock(CommandInterface::class);

        $messageBus->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willThrowException(new \Exception('Dispatch failed'));

        $adapter = new CommandBusAdapter($messageBus);
        $adapter->execute($command);
    }

    /**
     * @throws \Throwable
     */
    public function testExecuteThrowsPreviousException(): void
    {
        $this->expectException(ExceptionInterface::class);

        $messageBus = $this->createMock(MessageBusInterface::class);
        $command = $this->createMock(CommandInterface::class);
        $previousException = $this->createMock(ExceptionInterface::class);
        $exception = new \Exception('Dispatch failed', 0, $previousException);

        $messageBus->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willThrowException($exception);

        $adapter = new CommandBusAdapter($messageBus);
        $adapter->execute($command);
    }
}

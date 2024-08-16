<?php

declare(strict_types=1);

namespace App\Tests\Application\User\Command;

use App\Application\User\Command\ResetUserPasswordCommand;
use App\Domain\User\Dto\ResetUserPasswordDtoInterface;
use App\Domain\User\Entity\User;
use PHPUnit\Framework\TestCase;

class ResetUserPasswordCommandTest extends TestCase
{
    public function testConstructorSetsProperties(): void
    {
        $resetUserPasswordDto = $this->createMock(ResetUserPasswordDtoInterface::class);
        $user = $this->createMock(User::class);
        $command = new ResetUserPasswordCommand($resetUserPasswordDto, $user);

        $this->assertSame($resetUserPasswordDto, $command->getResetUserPasswordDto());
        $this->assertSame($user, $command->getUser());
    }

    public function testGetResetUserPasswordDtoReturnsDto(): void
    {
        $resetUserPasswordDto = $this->createMock(ResetUserPasswordDtoInterface::class);
        $user = $this->createMock(User::class);
        $command = new ResetUserPasswordCommand($resetUserPasswordDto, $user);

        $this->assertInstanceOf(ResetUserPasswordDtoInterface::class, $command->getResetUserPasswordDto());
        $this->assertSame($resetUserPasswordDto, $command->getResetUserPasswordDto());
    }

    public function testGetUserReturnsUser(): void
    {
        $resetUserPasswordDto = $this->createMock(ResetUserPasswordDtoInterface::class);
        $user = $this->createMock(User::class);
        $command = new ResetUserPasswordCommand($resetUserPasswordDto, $user);

        $this->assertInstanceOf(User::class, $command->getUser());
        $this->assertSame($user, $command->getUser());
    }
}
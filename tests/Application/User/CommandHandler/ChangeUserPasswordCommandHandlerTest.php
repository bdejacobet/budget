<?php

declare(strict_types=1);

namespace App\Tests\Application\User\CommandHandler;

use App\Application\User\Command\ChangeUserPasswordCommand;
use App\Application\User\CommandHandler\ChangeUserPasswordCommandHandler;
use App\Domain\User\Dto\ChangeUserPasswordDtoInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Exception\UserOldPasswordIsIncorrectException;
use App\Domain\User\Repository\UserCommandRepositoryInterface;
use App\Domain\User\Adapter\PasswordHasherInterface;
use PHPUnit\Framework\TestCase;

class ChangeUserPasswordCommandHandlerTest extends TestCase
{
    /**
     * @throws UserOldPasswordIsIncorrectException
     */
    public function testSuccessfulPasswordChange(): void
    {
        $changePasswordDto = $this->createMock(ChangeUserPasswordDtoInterface::class);
        $user = new User();
        $userCommandRepository = $this->createMock(UserCommandRepositoryInterface::class);
        $passwordHasher = $this->createMock(PasswordHasherInterface::class);

        $changePasswordDto->method('getOldPassword')->willReturn('oldPassword');
        $changePasswordDto->method('getNewPassword')->willReturn('newPassword');
        $passwordHasher->method('verify')->willReturn(true);
        $passwordHasher->method('hash')->willReturn('hashedNewPassword');

        $userCommandRepository->expects($this->once())->method('save')->with($user);

        $command = new ChangeUserPasswordCommand($changePasswordDto, $user);
        $handler = new ChangeUserPasswordCommandHandler($userCommandRepository, $passwordHasher);

        $handler->__invoke($command);
    }

    /**
     * @throws UserOldPasswordIsIncorrectException
     */
    public function testPasswordChangeWithIncorrectOldPassword(): void
    {
        $this->expectException(UserOldPasswordIsIncorrectException::class);

        $changePasswordDto = $this->createMock(ChangeUserPasswordDtoInterface::class);
        $user = new User();
        $userCommandRepository = $this->createMock(UserCommandRepositoryInterface::class);
        $passwordHasher = $this->createMock(PasswordHasherInterface::class);

        $changePasswordDto->method('getOldPassword')->willReturn('oldPassword');
        $passwordHasher->method('verify')->willReturn(false);

        $command = new ChangeUserPasswordCommand($changePasswordDto, $user);
        $handler = new ChangeUserPasswordCommandHandler($userCommandRepository, $passwordHasher);

        $handler->__invoke($command);
    }
}
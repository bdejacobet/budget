<?php

declare(strict_types=1);

namespace App\Domain\User\Model;

use App\Domain\Envelope\Model\EnvelopeInterface;
use App\Domain\Shared\Model\Collection;
use App\Domain\Shared\Model\UserInterface;

class UserModel implements UserInterface
{
    private int $id;
    private string $email;
    private string $password;
    private string $firstname;
    private string $lastname;
    private bool $consentGiven;
    /** @var array<string> */
    private array $roles = ['ROLE_USER'];
    private \DateTimeImmutable $consentDate;
    private \DateTimeImmutable $createdAt;
    private \DateTime $updatedAt;
    private \ArrayAccess|\IteratorAggregate|\Serializable|\Countable $envelopes;
    private ?string $passwordResetToken = null;
    private ?\DateTimeImmutable $passwordResetTokenExpiry = null;

    public function __construct()
    {
        $this->envelopes = new Collection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function isConsentGiven(): bool
    {
        return $this->consentGiven;
    }

    public function setConsentGiven(bool $consentGiven): self
    {
        $this->consentGiven = $consentGiven;

        return $this;
    }

    public function getConsentDate(): \DateTimeImmutable
    {
        return $this->consentDate;
    }

    /**
     * @return array<string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array<string> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function setConsentDate(\DateTimeImmutable $consentDate): self
    {
        $this->consentDate = $consentDate;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getEnvelopes(): \ArrayAccess|\IteratorAggregate|\Serializable|\Countable
    {
        return $this->envelopes;
    }

    public function setEnvelopes(\ArrayAccess|\IteratorAggregate|\Serializable|\Countable $envelopes): self
    {
        $this->envelopes = $envelopes;

        return $this;
    }

    public function addEnvelope(EnvelopeInterface $envelope): self
    {
        if (!$this->envelopes->contains($envelope)) {
            $this->envelopes->add($envelope);
            $envelope->setUser($this);
        }

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(?string $passwordResetToken): self
    {
        $this->passwordResetToken = $passwordResetToken;

        return $this;
    }

    public function getPasswordResetTokenExpiry(): ?\DateTimeImmutable
    {
        return $this->passwordResetTokenExpiry;
    }

    public function setPasswordResetTokenExpiry(?\DateTimeImmutable $passwordResetTokenExpiry): self
    {
        $this->passwordResetTokenExpiry = $passwordResetTokenExpiry;

        return $this;
    }
}

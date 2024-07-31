<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\Envelope\Entity\EnvelopeCollectionInterface;
use App\Domain\Envelope\Entity\EnvelopeInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class User implements UserInterface, \Symfony\Component\Security\Core\User\UserInterface, PasswordAuthenticatedUserInterface
{
    private int $id;
    private string $email;
    private string $password;
    private string $firstname;
    private string $lastname;
    private bool $consentGiven;
    private array $roles = ['ROLE_USER'];
    private \DateTimeImmutable $consentDate;
    private \DateTimeImmutable $createdAt;
    private \DateTime $updatedAt;
    private EnvelopeCollectionInterface|iterable $envelopes;

    public function __construct()
    {
        $this->envelopes = [];
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTime();
        $this->consentDate = new \DateTimeImmutable();
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

    public function getRoles(): array
    {
        return $this->roles;
    }

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

    public function setCreatedAt(\DateTimeImmutable $createdAt): User
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): User
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getEnvelopes(): EnvelopeCollectionInterface|iterable
    {
        return $this->envelopes;
    }

    public function setEnvelopes(EnvelopeCollectionInterface|iterable $envelopes): self
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
}

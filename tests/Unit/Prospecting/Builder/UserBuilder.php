<?php

declare(strict_types=1);

namespace Tests\Unit\Prospecting\Builder;

use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\Shared\Email;
use Solean\Prospecting\Domain\Model\Shared\Logo;
use Solean\Prospecting\Domain\Model\Shared\PersonName;
use Solean\Prospecting\Domain\Model\Shared\PhoneNumber;
use Solean\Prospecting\Domain\Model\User\User;
use Solean\Prospecting\Domain\Model\User\UserId;

final class UserBuilder
{
    private string $id = '1';
    private string $userName = 'john.doe';
    private string $organizationId = '100';
    private ?string $firstName = 'John';
    private ?string $lastName = 'Doe';
    private ?string $email = null;
    private ?string $phoneNumber = null;
    private ?string $language = 'EN';
    private array $roles = [];
    private ?Logo $picture = null;

    public static function aUser(): self
    {
        return new self();
    }

    public function withId(string $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function withUserName(string $userName): self
    {
        $clone = clone $this;
        $clone->userName = $userName;
        return $clone;
    }

    public function withOrganizationId(string $organizationId): self
    {
        $clone = clone $this;
        $clone->organizationId = $organizationId;
        return $clone;
    }

    public function withFirstName(?string $firstName): self
    {
        $clone = clone $this;
        $clone->firstName = $firstName;
        return $clone;
    }

    public function withLastName(?string $lastName): self
    {
        $clone = clone $this;
        $clone->lastName = $lastName;
        return $clone;
    }

    public function withEmail(?string $email): self
    {
        $clone = clone $this;
        $clone->email = $email;
        return $clone;
    }

    public function withPhoneNumber(?string $phoneNumber): self
    {
        $clone = clone $this;
        $clone->phoneNumber = $phoneNumber;
        return $clone;
    }

    public function withLanguage(?string $language): self
    {
        $clone = clone $this;
        $clone->language = $language;
        return $clone;
    }

    public function withPicture(?Logo $picture): self
    {
        $clone = clone $this;
        $clone->picture = $picture;
        return $clone;
    }

    public function build(): User
    {
        $user = User::create(
            id: UserId::fromString($this->id),
            userName: $this->userName,
            organizationId: OrganizationId::fromString($this->organizationId),
            name: PersonName::fromParts($this->firstName, $this->lastName),
            email: Email::tryFromString($this->email),
            phoneNumber: PhoneNumber::tryFromString($this->phoneNumber),
            language: $this->language,
            roles: $this->roles,
        );

        if ($this->picture !== null) {
            $user->attachPicture($this->picture);
        }

        return $user;
    }
}

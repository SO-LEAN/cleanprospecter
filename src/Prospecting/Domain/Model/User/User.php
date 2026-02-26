<?php

declare(strict_types=1);

namespace Solean\Prospecting\Domain\Model\User;

use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\Shared\Email;
use Solean\Prospecting\Domain\Model\Shared\Logo;
use Solean\Prospecting\Domain\Model\Shared\PersonName;
use Solean\Prospecting\Domain\Model\Shared\PhoneNumber;

final class User
{
    private string $userName;
    private ?string $password;
    private ?string $salt;
    private ?PersonName $name;
    private ?Email $email;
    private ?PhoneNumber $phoneNumber;
    private ?string $language;
    private ?Logo $picture;
    private OrganizationId $organizationId;
    /** @var string[] */
    private array $roles;

    private function __construct(
        private readonly UserId $id,
    ) {
        $this->roles = [];
        $this->picture = null;
        $this->name = null;
        $this->email = null;
        $this->phoneNumber = null;
        $this->language = null;
        $this->password = null;
        $this->salt = null;
    }

    public static function create(
        UserId $id,
        string $userName,
        OrganizationId $organizationId,
        ?PersonName $name = null,
        ?Email $email = null,
        ?PhoneNumber $phoneNumber = null,
        ?string $language = null,
        array $roles = [],
    ): self {
        $user = new self($id);
        $user->userName = $userName;
        $user->organizationId = $organizationId;
        $user->name = $name;
        $user->email = $email;
        $user->phoneNumber = $phoneNumber;
        $user->language = $language;
        $user->roles = $roles;

        return $user;
    }

    public function updateAccount(
        string $userName,
        ?PersonName $name,
        ?Email $email,
        ?PhoneNumber $phoneNumber,
        ?string $language,
        ?string $password = null,
    ): void {
        $this->userName = $userName;
        $this->name = $name;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->language = $language;

        if ($password !== null) {
            $this->password = $password;
            $this->encodePassword();
        }
    }

    public function attachPicture(Logo $picture): void
    {
        $this->picture = $picture;
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function userName(): string
    {
        return $this->userName;
    }

    public function name(): ?PersonName
    {
        return $this->name;
    }

    public function email(): ?Email
    {
        return $this->email;
    }

    public function phoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function language(): ?string
    {
        return $this->language;
    }

    public function picture(): ?Logo
    {
        return $this->picture;
    }

    public function organizationId(): OrganizationId
    {
        return $this->organizationId;
    }

    public function fullName(): string
    {
        return $this->name ? $this->name->fullName() : '';
    }

    private function encodePassword(): void
    {
        if ($this->password && $this->salt) {
            $this->password = md5(sprintf('%s%s', $this->password, $this->salt));
        }
    }
}

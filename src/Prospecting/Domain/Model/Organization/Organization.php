<?php

declare(strict_types=1);

namespace Solean\Prospecting\Domain\Model\Organization;

use Solean\Prospecting\Domain\Model\Shared\Address;
use Solean\Prospecting\Domain\Model\Shared\Email;
use Solean\Prospecting\Domain\Model\Shared\GeoPoint;
use Solean\Prospecting\Domain\Model\Shared\Logo;
use Solean\Prospecting\Domain\Model\Shared\PhoneNumber;
use Solean\Prospecting\Domain\Exception\ValidationException;

final class Organization
{
    use RaiseDomainEvents;

    private ?Email $email;
    private ?PhoneNumber $phoneNumber;
    private ?string $language;
    private ?string $corporateName;
    private ?string $form;
    private ?string $type;
    private ?string $observations;
    private ?Address $address;
    private ?GeoPoint $geoPoint;
    private ?OrganizationId $holdingId;
    private ?OrganizationId $ownerId;
    private ?Logo $logo;

    private function __construct(
        private readonly OrganizationId $id,
    ) {
        $this->email = null;
        $this->phoneNumber = null;
        $this->language = null;
        $this->corporateName = null;
        $this->form = null;
        $this->type = null;
        $this->observations = null;
        $this->address = null;
        $this->geoPoint = null;
        $this->holdingId = null;
        $this->ownerId = null;
        $this->logo = null;
    }

    public static function register(
        OrganizationId $id,
        OrganizationId $ownerId,
        ?string $corporateName = null,
        ?Email $email = null,
        ?PhoneNumber $phoneNumber = null,
        ?string $language = null,
        ?string $form = null,
        ?string $type = null,
        ?string $observations = null,
        ?Address $address = null,
        ?Logo $logo = null,
        ?OrganizationId $holdingId = null,
    ): self {
        $org = new self($id);
        $org->ownerId = $ownerId;
        $org->corporateName = $corporateName;
        $org->email = $email;
        $org->phoneNumber = $phoneNumber;
        $org->language = $language;
        $org->form = $form;
        $org->type = $type;
        $org->observations = $observations;
        $org->address = $address;
        $org->logo = $logo;
        $org->holdingId = $holdingId;

        $org->validate();
        $org->raise(new OrganizationRegistered($id));

        return $org;
    }

    public function updateProfile(
        ?string $corporateName,
        ?Email $email,
        ?PhoneNumber $phoneNumber,
        ?string $language,
        ?string $form,
        ?string $type,
        ?string $observations,
        ?Address $address,
        ?OrganizationId $holdingId,
    ): void {
        $this->corporateName = $corporateName;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->language = $language;
        $this->form = $form;
        $this->type = $type;
        $this->observations = $observations;
        $this->holdingId = $holdingId;

        if ($address !== null && $address !== $this->address) {
            $this->address = $address;
            $this->geoPoint = null;
        } elseif ($address === null) {
            $this->address = null;
            $this->geoPoint = null;
        }

        $this->validate();
    }

    public function relocate(Address $address, ?GeoPoint $geoPoint = null): void
    {
        $this->address = $address;
        $this->geoPoint = $geoPoint;
        $this->raise(new OrganizationRelocated($this->id));
    }

    public function pinpoint(?GeoPoint $geoPoint): void
    {
        $this->geoPoint = $geoPoint;
    }

    public function attachLogo(Logo $logo): void
    {
        $this->logo = $logo;
    }

    public function removeLogo(): void
    {
        $this->logo = null;
    }

    public function assignToHolding(OrganizationId $holdingId): void
    {
        $this->holdingId = $holdingId;
    }

    public function updateCorporateInfo(?string $corporateName, ?string $form, ?string $language): void
    {
        $this->corporateName = $corporateName;
        $this->form = $form;
        $this->language = $language;
    }

    // --- Internal getters for domain logic and read model mapping ---

    public function id(): OrganizationId
    {
        return $this->id;
    }

    public function ownerId(): ?OrganizationId
    {
        return $this->ownerId;
    }

    public function corporateName(): ?string
    {
        return $this->corporateName;
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

    public function form(): ?string
    {
        return $this->form;
    }

    public function type(): ?string
    {
        return $this->type;
    }

    public function observations(): ?string
    {
        return $this->observations;
    }

    public function address(): ?Address
    {
        return $this->address;
    }

    public function geoPoint(): ?GeoPoint
    {
        return $this->geoPoint;
    }

    public function holdingId(): ?OrganizationId
    {
        return $this->holdingId;
    }

    public function logo(): ?Logo
    {
        return $this->logo;
    }

    public function fullName(): string
    {
        return trim(sprintf('%s %s', $this->corporateName, $this->form));
    }

    private function validate(): void
    {
        if ($this->ownerId !== null && !$this->corporateName && !$this->email) {
            throw new ValidationException('At least one is mandatory : corporate name or email');
        }
    }
}

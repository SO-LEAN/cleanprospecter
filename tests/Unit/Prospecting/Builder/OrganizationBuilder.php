<?php

declare(strict_types=1);

namespace Tests\Unit\Prospecting\Builder;

use Solean\Prospecting\Domain\Model\Organization\Organization;
use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\Shared\Address;
use Solean\Prospecting\Domain\Model\Shared\Email;
use Solean\Prospecting\Domain\Model\Shared\GeoPoint;
use Solean\Prospecting\Domain\Model\Shared\Logo;
use Solean\Prospecting\Domain\Model\Shared\PhoneNumber;

final class OrganizationBuilder
{
    private string $id = '1';
    private string $ownerId = '100';
    private ?string $corporateName = 'ACME';
    private ?string $email = null;
    private ?string $phoneNumber = null;
    private ?string $language = 'EN';
    private ?string $form = 'Limited Company';
    private ?string $type = 'Consulting';
    private ?string $observations = null;
    private ?Address $address = null;
    private ?Logo $logo = null;
    private ?string $holdingId = null;
    private ?GeoPoint $geoPoint = null;

    public static function anOrganization(): self
    {
        return new self();
    }

    public function withId(string $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function withOwnerId(string $ownerId): self
    {
        $clone = clone $this;
        $clone->ownerId = $ownerId;
        return $clone;
    }

    public function withCorporateName(?string $name): self
    {
        $clone = clone $this;
        $clone->corporateName = $name;
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

    public function withForm(?string $form): self
    {
        $clone = clone $this;
        $clone->form = $form;
        return $clone;
    }

    public function withType(?string $type): self
    {
        $clone = clone $this;
        $clone->type = $type;
        return $clone;
    }

    public function withObservations(?string $observations): self
    {
        $clone = clone $this;
        $clone->observations = $observations;
        return $clone;
    }

    public function withAddress(?Address $address): self
    {
        $clone = clone $this;
        $clone->address = $address;
        return $clone;
    }

    public function withLogo(?Logo $logo): self
    {
        $clone = clone $this;
        $clone->logo = $logo;
        return $clone;
    }

    public function withHoldingId(?string $holdingId): self
    {
        $clone = clone $this;
        $clone->holdingId = $holdingId;
        return $clone;
    }

    public function withGeoPoint(?GeoPoint $geoPoint): self
    {
        $clone = clone $this;
        $clone->geoPoint = $geoPoint;
        return $clone;
    }

    public function build(): Organization
    {
        $org = Organization::register(
            id: OrganizationId::fromString($this->id),
            ownerId: OrganizationId::fromString($this->ownerId),
            corporateName: $this->corporateName,
            email: $this->email ? Email::fromString($this->email) : null,
            phoneNumber: $this->phoneNumber ? PhoneNumber::fromString($this->phoneNumber) : null,
            language: $this->language,
            form: $this->form,
            type: $this->type,
            observations: $this->observations,
            address: $this->address,
            logo: $this->logo,
            holdingId: $this->holdingId ? OrganizationId::fromString($this->holdingId) : null,
        );

        if ($this->geoPoint !== null) {
            $org->pinpoint($this->geoPoint);
        }

        return $org;
    }
}

<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Entity;

final class Organization extends Person
{
    /**
     * @var string
     */
    private $corporateName;
    /**
     * @var string
     */
    private $form;
    /**
     * @var Organization
     */
    private $holdBy;

    public function getCorporateName(): string
    {
        return $this->corporateName;
    }

    public function setCorporateName(string $corporateName): void
    {
        $this->corporateName = $corporateName;
    }

    public function getForm(): string
    {
        return $this->form;
    }

    public function setForm(string $form): void
    {
        $this->form = $form;
    }

    public function getHoldBy(): Organization
    {
        return $this->holdBy;
    }

    public function setHoldBy(Organization $holdBy): void
    {
        $this->holdBy = $holdBy;
    }

    public function getFullName(): string
    {
        return sprintf('%s %s', $this->corporateName, $this->form);
    }
}

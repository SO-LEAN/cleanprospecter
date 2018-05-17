<?php

namespace Solean\CleanProspecter\UseCase;

abstract class AbstractUseCase
{
    /**
     * empty mean everybody
     */
    public function canBeExecutedBy(): array
    {
        return [];
    }

    public function __toString()
    {
        return sprintf('As %s, I want to %s', $this->rolesAsWords(), $this->getUseCaseName());
    }

    private function getUseCaseName() : string
    {
        $result = preg_replace(['/(^[[:alnum:]\\\]+\\\)|(Impl$)/', '/([[:upper:]][[:lower:]]+)/'], ['', '\1 '], get_class($this));

        return trim(strtolower($result));
    }

    private function rolesAsWords(): string
    {
        $roles = empty($this->canBeExecutedBy()) ? ['anonymous'] : preg_replace('/^(role_)/', '', array_map('strtolower', $this->canBeExecutedBy()));
        $lastRole = array_pop($roles);

        if ($roles) {
            return sprintf('%s or %s', implode(', ', $roles), $lastRole);
        }

        return $lastRole;
    }
}

<?php

declare(strict_types=1);

namespace Solean\Prospecting\Infrastructure\Persistence\InMemory;

use Solean\Prospecting\Domain\Exception\OrganizationNotFoundException;
use Solean\Prospecting\Domain\Model\Organization\Organization;
use Solean\Prospecting\Domain\Model\Organization\OrganizationId;
use Solean\Prospecting\Domain\Model\Organization\OrganizationRepository;

final class InMemoryOrganizationRepository implements OrganizationRepository
{
    /** @var array<string, Organization> */
    private array $organizations = [];
    private int $sequence = 0;

    public function nextId(): OrganizationId
    {
        return OrganizationId::fromString((string) ++$this->sequence);
    }

    public function save(Organization $organization): void
    {
        $this->organizations[$organization->id()->value] = $organization;
    }

    public function ofId(OrganizationId $id): Organization
    {
        if (!isset($this->organizations[$id->value])) {
            throw OrganizationNotFoundException::withId($id->value);
        }

        return $this->organizations[$id->value];
    }

    public function remove(Organization $organization): void
    {
        unset($this->organizations[$organization->id()->value]);
    }

    public function count(): int
    {
        return count($this->organizations);
    }

    public function findPageByOwner(OrganizationId $ownerId, int $page = 1, int $maxPerPage = 20, string $query = ''): array
    {
        $filtered = array_filter(
            $this->organizations,
            static function (Organization $org) use ($ownerId, $query): bool {
                if ($org->ownerId()?->value !== $ownerId->value) {
                    return false;
                }
                if ($query !== '' && !str_contains(strtolower($org->fullName()), strtolower($query))) {
                    return false;
                }
                return true;
            },
        );

        $total = count($filtered);
        $totalPages = $maxPerPage > 0 ? (int) ceil($total / $maxPerPage) : 1;
        $offset = ($page - 1) * $maxPerPage;
        $organizations = array_slice(array_values($filtered), $offset, $maxPerPage);

        return [
            'organizations' => $organizations,
            'total' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page,
        ];
    }
}

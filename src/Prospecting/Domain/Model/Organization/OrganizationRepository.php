<?php

declare(strict_types=1);

namespace Solean\Prospecting\Domain\Model\Organization;

interface OrganizationRepository
{
    public function nextId(): OrganizationId;

    public function save(Organization $organization): void;

    public function ofId(OrganizationId $id): Organization;

    public function remove(Organization $organization): void;

    /**
     * @return array{organizations: Organization[], total: int, totalPages: int, currentPage: int}
     */
    public function findPageByOwner(OrganizationId $ownerId, int $page = 1, int $maxPerPage = 20, string $query = ''): array;
}

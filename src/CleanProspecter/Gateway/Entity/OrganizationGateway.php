<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Gateway\Entity;

use Solean\CleanProspecter\Entity\Organization;

interface OrganizationGateway
{
    public function get($id): Organization;
    public function create(Organization $organization): Organization;
    public function update($id, Organization $organization): Organization;
    public function findOneBy(array $criteria): ?Organization;
    public function findBy(array $criteria): array;
    public function findPageByQuery(PageRequest $pageRequest): Page;
}

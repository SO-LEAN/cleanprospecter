<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Gateway\Entity;

use Solean\CleanProspecter\Entity\User;

interface UserGateway
{
    public function get($id): User;
    public function create(User $user): User;
    public function save($id, User $user): User;
    public function findOneBy(array $criteria): ?User;
    public function findBy(array $criteria): array;
}

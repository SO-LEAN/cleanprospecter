<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Gateway\Entity;

use Solean\CleanProspecter\Entity\User;

interface UserGateway
{
    public function getUser($id) : User;
    public function createUser(User $user) : User;
    public function saveUser($id, User $user) : User;
    public function findOneBy(array $criteria) : ?User;
    public function findBy(array $criteria) : array;
}

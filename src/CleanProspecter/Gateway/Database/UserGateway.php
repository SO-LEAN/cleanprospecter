<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Gateway\Database;

use Solean\CleanProspecter\Entity\User;

interface UserGateway extends TransactionGateway
{
    public function getUser(User $entity) : User;
    public function createUser(User $entity) : void;
    public function saveUser(User $entity) : void;
    public function findOneBy(array $criteria) : ?User;
    public function findBy(array $criteria) : array;
}
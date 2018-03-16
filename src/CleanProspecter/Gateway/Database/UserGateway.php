<?php
namespace Solean\CleanProspector\Gateway\Database;

use Solean\CleanProspector\Entity\User;

interface UserGateway extends TransactionGateway
{
    public function getUser(User $entity) : void;
    public function createUser(User $entity) : void;
    public function saveUser(User $entity) : void;
    public function findOneBy(array $criteria) : Collection;
    public function findBy(array $criteria) : Collection;
}
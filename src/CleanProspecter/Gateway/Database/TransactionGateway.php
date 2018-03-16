<?php
namespace Solean\CleanProspector\Gateway\Database;

interface TransactionGateway
{
    public function startTransaction() : void;
    public function commit() : void;
    public function rollback() : void;
}
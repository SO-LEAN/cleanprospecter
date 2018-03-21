<?php
namespace Solean\CleanProspecter\Gateway\Database;

interface TransactionGateway
{
    public function commit() : void;
}
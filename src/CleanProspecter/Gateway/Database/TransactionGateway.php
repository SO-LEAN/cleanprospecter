<?php
namespace Solean\CleanProspector\Gateway\Database;

interface TransactionGateway
{
    public function commit() : void;
}
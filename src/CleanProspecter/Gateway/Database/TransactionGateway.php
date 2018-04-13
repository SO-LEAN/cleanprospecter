<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\Gateway\Database;

interface TransactionGateway
{
    public function commit() : void;
}

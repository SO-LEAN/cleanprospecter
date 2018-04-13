<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase;

interface Presenter
{
    public function present(object $response) : object;
}

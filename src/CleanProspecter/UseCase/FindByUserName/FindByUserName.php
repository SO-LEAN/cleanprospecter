<?php
namespace Solean\CleanProspecter\UseCase\FindByUserName;

use Solean\CleanProspecter\UseCase\Presenter;

interface FindByUserName
{
    public function execute(FindByUserNameRequest $request, Presenter $presenter) : ?object;
}

<?php
namespace Solean\CleanProspecter\UseCase\RefreshUser;

interface RefreshUser
{
    public function execute(RefreshUserRequest $request, RefreshUserPresenter $presenter);
}

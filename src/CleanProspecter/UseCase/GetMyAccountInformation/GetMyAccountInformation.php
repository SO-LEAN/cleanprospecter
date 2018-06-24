<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\GetMyAccountInformation;

use Solean\CleanProspecter\UseCase\UseCaseConsumer;

interface GetMyAccountInformation
{
    public function execute(GetMyAccountInformationRequest $request, GetMyAccountInformationPresenter $presenter, UseCaseConsumer $consumer);
}

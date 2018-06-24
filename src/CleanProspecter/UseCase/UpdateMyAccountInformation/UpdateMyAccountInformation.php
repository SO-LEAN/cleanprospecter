<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\UpdateMyAccountInformation;

use Solean\CleanProspecter\UseCase\UseCaseConsumer;

interface UpdateMyAccountInformation
{
    public function execute(UpdateMyAccountInformationRequest $request, UpdateMyAccountInformationPresenter $presenter, UseCaseConsumer $consumer): ?object;
}

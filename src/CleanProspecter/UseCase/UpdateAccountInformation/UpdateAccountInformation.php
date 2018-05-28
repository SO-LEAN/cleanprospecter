<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\UpdateAccountInformation;

use Solean\CleanProspecter\UseCase\UseCaseConsumer;

interface UpdateAccountInformation
{
    public function execute(UpdateAccountInformationRequest $request, UpdateAccountInformationPresenter $presenter, UseCaseConsumer $useCaseConsumer): ?object;
}

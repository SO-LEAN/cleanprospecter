<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\UpdateAccountInformation;

interface UpdateAccountInformationPresenter
{
    public function present(UpdateAccountInformationResponse $response);
}

<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\UpdateMyAccountInformation;

interface UpdateMyAccountInformationPresenter
{
    public function present(UpdateMyAccountInformationResponse $response);
}

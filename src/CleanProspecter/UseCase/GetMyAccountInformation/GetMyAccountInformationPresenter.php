<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase\GetMyAccountInformation;

interface GetMyAccountInformationPresenter
{
    public function present(GetMyAccountInformationResponse $response);
}

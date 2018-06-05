<?php

namespace Solean\CleanProspecter\Traits\UseCase;

use Solean\CleanProspecter\Gateway\UserNotifier;

trait UserNotifierTrait
{
    /**
     * @var UserNotifier
     */
    private $userNotifier;


    private function notifySuccess(string $msg)
    {
        $this->userNotifier->addSuccess($msg);
    }
}

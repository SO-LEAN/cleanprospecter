<?php

declare( strict_types = 1 );

namespace Solean\CleanProspecter\UseCase;

abstract class AuthenticatedRequest implements UseCaseRequest
{
    /**
     * @var string
     */
    private $userId;


    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function isAuthenticated(): bool
    {
        return !empty($this->userId);
    }
}

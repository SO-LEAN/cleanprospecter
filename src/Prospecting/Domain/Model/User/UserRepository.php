<?php

declare(strict_types=1);

namespace Solean\Prospecting\Domain\Model\User;

interface UserRepository
{
    public function nextId(): UserId;

    public function save(User $user): void;

    public function ofId(UserId $id): User;
}

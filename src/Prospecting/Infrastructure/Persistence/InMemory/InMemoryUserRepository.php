<?php

declare(strict_types=1);

namespace Solean\Prospecting\Infrastructure\Persistence\InMemory;

use Solean\Prospecting\Domain\Exception\UserNotFoundException;
use Solean\Prospecting\Domain\Model\User\User;
use Solean\Prospecting\Domain\Model\User\UserId;
use Solean\Prospecting\Domain\Model\User\UserRepository;

final class InMemoryUserRepository implements UserRepository
{
    /** @var array<string, User> */
    private array $users = [];
    private int $sequence = 0;

    public function nextId(): UserId
    {
        return UserId::fromString((string) ++$this->sequence);
    }

    public function save(User $user): void
    {
        $this->users[$user->id()->value] = $user;
    }

    public function ofId(UserId $id): User
    {
        if (!isset($this->users[$id->value])) {
            throw UserNotFoundException::withId($id->value);
        }

        return $this->users[$id->value];
    }

    public function count(): int
    {
        return count($this->users);
    }
}

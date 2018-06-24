<?php

namespace Tests\Unit\Solean\CleanProspecter\UseCase;

use PHPUnit\Framework\TestCase;
use Tests\Unit\Solean\CleanProspecter\UseCase\Stub\StubPublicUseCaseImpl;

class AbstractUserCaseTest extends TestCase
{
    /**
     * @dataProvider provideUseCases
     */
    public function testUseCaseAsString(string $expected, array $roles)
    {
        $useCase = new StubPublicUseCaseImpl($roles);

        $this->assertEquals($expected, (string) $useCase);
    }

    public function provideUseCases()
    {
        (yield 'anonymous' => ['As anonymous, I want to stub public use case', []]);
        (yield 'one role' => ['As developer, I want to stub public use case', ['ROLE_DEVELOPER']]);
        (yield 'multirole' => ['As developer, project_manager or scrum_master, I want to stub public use case', ['ROLE_DEVELOPER', 'ROLE_PROJECT_MANAGER', 'ROLE_SCRUM_MASTER']]);
    }
}

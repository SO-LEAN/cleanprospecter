<?php

declare( strict_types = 1 );

namespace Tests\Unit\Solean\CleanProspecter\UseCase\UpdateOrganization;

use Solean\CleanProspecter\Entity\Organization;
use Tests\Unit\Solean\Base\TestCase;
use Solean\CleanProspecter\Gateway\Entity\OrganizationGateway;
use Solean\CleanProspecter\Gateway\Storage;
use Solean\CleanProspecter\Gateway\UserNotifier;
use Solean\CleanProspecter\UseCase\UpdateOrganization\UpdateOrganizationImpl;

use function Tests\Unit\Solean\Base\anOrganization;
use function Tests\Unit\Solean\Base\anAddress;
use function Tests\Unit\Solean\Base\aFile;

class UpdateOrganizationImplTest extends TestCase
{
    public function target() : UpdateOrganizationImpl
    {
        return parent::target();
    }

    public function setupArgs() : array
    {
        return [
            $this->prophesy(OrganizationGateway::class)->reveal(),
            $this->prophesy(Storage::class)->reveal(),
            $this->prophesy(UserNotifier::class)->reveal(),
        ];
    }

    public function testCanUpdateUseCase()
    {
        $this->assertInstanceOf($this->getTargetClassName(), $this->target());
    }
}

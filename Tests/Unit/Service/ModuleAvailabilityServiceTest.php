<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\Mysqlreport\Service\DatabaseCapabilityServiceInterface;
use StefanFroemken\Mysqlreport\Service\ModuleAvailabilityService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ModuleAvailabilityServiceTest extends UnitTestCase
{
    #[Test]
    public function isModuleAvailableReturnsFalseForQueryCacheWhenCapabilityUnsupported(): void
    {
        $capabilityService = $this->createMock(DatabaseCapabilityServiceInterface::class);
        $capabilityService->expects($this->once())
            ->method('isQueryCacheSupported')
            ->willReturn(false);

        $service = new ModuleAvailabilityService($capabilityService);

        self::assertFalse($service->isModuleAvailable('mysqlreport_querycache'));
    }

    #[Test]
    public function isModuleAvailableReturnsTrueForQueryCacheWhenCapabilitySupported(): void
    {
        $capabilityService = $this->createMock(DatabaseCapabilityServiceInterface::class);
        $capabilityService->expects($this->once())
            ->method('isQueryCacheSupported')
            ->willReturn(true);

        $service = new ModuleAvailabilityService($capabilityService);

        self::assertTrue($service->isModuleAvailable('mysqlreport_querycache'));
    }

    #[Test]
    public function isModuleAvailableReturnsTrueForOtherModules(): void
    {
        $capabilityService = $this->createMock(DatabaseCapabilityServiceInterface::class);
        $capabilityService->expects($this->never())->method('isQueryCacheSupported');

        $service = new ModuleAvailabilityService($capabilityService);

        self::assertTrue($service->isModuleAvailable('mysqlreport_innodb'));
        self::assertTrue($service->isModuleAvailable('mysqlreport_status'));
    }
}

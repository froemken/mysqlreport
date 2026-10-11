<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\EventListener;

use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\Mysqlreport\EventListener\BeforeModuleCreationEventListener;
use StefanFroemken\Mysqlreport\Service\ModuleAvailabilityServiceInterface;
use TYPO3\CMS\Backend\Module\BeforeModuleCreationEvent;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class BeforeModuleCreationEventListenerTest extends UnitTestCase
{
    #[Test]
    public function invokeLeavesConfigurationUntouchedWhenModuleIsAvailable(): void
    {
        $availabilityService = $this->createMock(ModuleAvailabilityServiceInterface::class);
        $availabilityService->expects($this->once())
            ->method('isModuleAvailable')
            ->with('mysqlreport_querycache')
            ->willReturn(true);

        $initialConfig = ['parent' => 'mysqlreport', 'access' => 'admin'];
        $event = new BeforeModuleCreationEvent('mysqlreport_querycache', $initialConfig);

        $listener = new BeforeModuleCreationEventListener($availabilityService);
        $listener($event);

        self::assertSame($initialConfig, $event->getConfiguration());
    }

    #[Test]
    public function invokeDisablesModuleWhenModuleIsUnavailable(): void
    {
        $availabilityService = $this->createMock(ModuleAvailabilityServiceInterface::class);
        $availabilityService->expects($this->once())
            ->method('isModuleAvailable')
            ->with('mysqlreport_querycache')
            ->willReturn(false);

        $initialConfig = ['parent' => 'mysqlreport', 'access' => 'admin'];
        $event = new BeforeModuleCreationEvent('mysqlreport_querycache', $initialConfig);

        $listener = new BeforeModuleCreationEventListener($availabilityService);
        $listener($event);

        self::assertFalse($event->getConfigurationValue('appearance')['renderInModuleMenu'] ?? true);
        self::assertSame('mysqlreport_unavailable', $event->getConfigurationValue('access'));
    }
}

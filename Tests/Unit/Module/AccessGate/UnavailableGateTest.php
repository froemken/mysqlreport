<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\Module\AccessGate;

use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\Mysqlreport\Module\AccessGate\UnavailableGate;
use TYPO3\CMS\Backend\Module\ModuleAccessResult;
use TYPO3\CMS\Backend\Module\ModuleInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class UnavailableGateTest extends UnitTestCase
{
    #[Test]
    public function decideReturnsAbstainWhenAccessDoesNotMatch(): void
    {
        $module = $this->createMock(ModuleInterface::class);
        $module->method('getAccess')->willReturn('admin');
        $user = $this->createMock(BackendUserAuthentication::class);

        $gate = new UnavailableGate();
        self::assertSame(ModuleAccessResult::Abstain, $gate->decide($module, $user));
    }

    #[Test]
    public function decideReturnsDeniedWhenAccessMatches(): void
    {
        $module = $this->createMock(ModuleInterface::class);
        $module->method('getAccess')->willReturn('mysqlreport_unavailable');
        $user = $this->createMock(BackendUserAuthentication::class);

        $gate = new UnavailableGate();
        self::assertSame(ModuleAccessResult::Denied, $gate->decide($module, $user));
    }
}

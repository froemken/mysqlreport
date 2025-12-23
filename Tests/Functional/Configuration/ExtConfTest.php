<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Functional\Configuration;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use StefanFroemken\Mysqlreport\Configuration\ExtConf;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Test case.
 */
class ExtConfTest extends FunctionalTestCase
{
    private ExtensionConfiguration|MockObject $extensionConfigurationMock;

    protected array $testExtensionsToLoad = [
        'stefanfroemken/mysqlreport',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->extensionConfigurationMock = $this->createMock(ExtensionConfiguration::class);
    }

    protected function tearDown(): void
    {
        unset(
            $this->extensionConfigurationMock,
        );

        parent::tearDown();
    }

    #[Test]
    public function isEnableFrontendLoggingInitiallyReturnsFalse(): void
    {
        $this->extensionConfigurationMock
            ->expects(self::once())
            ->method('get')
            ->with('mysqlreport')
            ->willReturn([]);

        $subject = ExtConf::create($this->extensionConfigurationMock);

        self::assertFalse(
            $subject->isEnableFrontendLogging(),
        );
    }

    #[Test]
    public function setEnableFrontendLoggingSetsEnableFrontendLogging(): void
    {
        $this->extensionConfigurationMock
            ->expects(self::once())
            ->method('get')
            ->with('mysqlreport')
            ->willReturn([
                'enableFrontendLogging' => '1',
            ]);

        $subject = ExtConf::create($this->extensionConfigurationMock);

        self::assertTrue(
            $subject->isEnableFrontendLogging(),
        );
    }

    #[Test]
    public function isEnableBackendLoggingInitiallyReturnsFalse(): void
    {
        $this->extensionConfigurationMock
            ->expects(self::once())
            ->method('get')
            ->with('mysqlreport')
            ->willReturn([]);

        $subject = ExtConf::create($this->extensionConfigurationMock);

        self::assertFalse(
            $subject->isEnableBackendLogging(),
        );
    }

    #[Test]
    public function setEnableBackendLoggingSetsEnableBackendLogging(): void
    {
        $this->extensionConfigurationMock
            ->expects(self::once())
            ->method('get')
            ->with('mysqlreport')
            ->willReturn([
                'enableBackendLogging' => '1',
            ]);

        $subject = ExtConf::create($this->extensionConfigurationMock);

        self::assertTrue(
            $subject->isEnableBackendLogging(),
        );
    }

    #[Test]
    public function isActivateExplainQueryInitiallyReturnsFalse(): void
    {
        $this->extensionConfigurationMock
            ->expects(self::once())
            ->method('get')
            ->with('mysqlreport')
            ->willReturn([]);

        $subject = ExtConf::create($this->extensionConfigurationMock);

        self::assertFalse(
            $subject->isActivateExplainQuery(),
        );
    }

    #[Test]
    public function setActivateExplainQuerySetsActivateExplainQuery(): void
    {
        $this->extensionConfigurationMock
            ->expects(self::once())
            ->method('get')
            ->with('mysqlreport')
            ->willReturn([
                'activateExplainQuery' => '1',
            ]);

        $subject = ExtConf::create($this->extensionConfigurationMock);

        self::assertTrue(
            $subject->isActivateExplainQuery(),
        );
    }

    #[Test]
    public function getSlowQueryThresholdInitiallyReturns10Seconds(): void
    {
        $this->extensionConfigurationMock
            ->expects(self::once())
            ->method('get')
            ->with('mysqlreport')
            ->willReturn([]);

        $subject = ExtConf::create($this->extensionConfigurationMock);

        self::assertSame(
            10.0,
            $subject->getSlowQueryThreshold(),
        );
    }

    #[Test]
    public function setSlowQueryThresholdWithIntegerSetsSlowQueryThreshold(): void
    {
        $this->extensionConfigurationMock
            ->expects(self::once())
            ->method('get')
            ->with('mysqlreport')
            ->willReturn([
                'slowQueryThreshold' => '1',
            ]);

        $subject = ExtConf::create($this->extensionConfigurationMock);

        self::assertSame(
            1.0,
            $subject->getSlowQueryThreshold(),
        );
    }

    #[Test]
    public function setSlowQueryThresholdWithFloatSetsSlowQueryThreshold(): void
    {
        $this->extensionConfigurationMock
            ->expects(self::once())
            ->method('get')
            ->with('mysqlreport')
            ->willReturn([
                'slowQueryThreshold' => '1.25',
            ]);

        $subject = ExtConf::create($this->extensionConfigurationMock);

        self::assertSame(
            1.25,
            $subject->getSlowQueryThreshold(),
        );
    }

    #[Test]
    public function setSlowQueryThresholdWithCommaFloatSetsSlowQueryThreshold(): void
    {
        $this->extensionConfigurationMock
            ->expects(self::once())
            ->method('get')
            ->with('mysqlreport')
            ->willReturn([
                'slowQueryThreshold' => '5,38',
            ]);

        $subject = ExtConf::create($this->extensionConfigurationMock);

        self::assertSame(
            5.38,
            $subject->getSlowQueryThreshold(),
        );
    }
}

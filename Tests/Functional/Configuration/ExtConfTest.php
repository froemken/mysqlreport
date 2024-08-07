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
use StefanFroemken\Mysqlreport\Configuration\ExtConf;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Test case.
 */
class ExtConfTest extends FunctionalTestCase
{
    private ExtConf $subject;

    protected array $testExtensionsToLoad = [
        'stefanfroemken/mysqlreport',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->get(ExtConf::class);
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject,
        );

        parent::tearDown();
    }

    #[Test]
    public function isEnableFrontendLoggingInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->subject->isEnableFrontendLogging(),
        );
    }

    #[Test]
    public function setEnableFrontendLoggingSetsEnableFrontendLogging(): void
    {
        $this->subject->setEnableFrontendLogging('1');

        self::assertTrue(
            $this->subject->isEnableFrontendLogging(),
        );
    }

    #[Test]
    public function isEnableBackendLoggingInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->subject->isEnableBackendLogging(),
        );
    }

    #[Test]
    public function setEnableBackendLoggingSetsEnableBackendLogging(): void
    {
        $this->subject->setEnableBackendLogging('1');

        self::assertTrue(
            $this->subject->isEnableBackendLogging(),
        );
    }

    #[Test]
    public function isActivateExplainQueryInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->subject->isActivateExplainQuery(),
        );
    }

    #[Test]
    public function setActivateExplainQuerySetsActivateExplainQuery(): void
    {
        $this->subject->setActivateExplainQuery('1');

        self::assertTrue(
            $this->subject->isActivateExplainQuery(),
        );
    }

    #[Test]
    public function getSlowQueryThresholdInitiallyReturns10Seconds(): void
    {
        self::assertSame(
            10.0,
            $this->subject->getSlowQueryThreshold(),
        );
    }

    #[Test]
    public function setSlowQueryThresholdWithIntegerSetsSlowQueryThreshold(): void
    {
        $this->subject->setSlowQueryThreshold('1');

        self::assertSame(
            1.0,
            $this->subject->getSlowQueryThreshold(),
        );
    }

    #[Test]
    public function setSlowQueryThresholdWithFloatSetsSlowQueryThreshold(): void
    {
        $this->subject->setSlowQueryThreshold('1.25');

        self::assertSame(
            1.25,
            $this->subject->getSlowQueryThreshold(),
        );
    }

    #[Test]
    public function setSlowQueryThresholdWithCommaFloatSetsSlowQueryThreshold(): void
    {
        $this->subject->setSlowQueryThreshold('5,38');

        self::assertSame(
            5.38,
            $this->subject->getSlowQueryThreshold(),
        );
    }
}

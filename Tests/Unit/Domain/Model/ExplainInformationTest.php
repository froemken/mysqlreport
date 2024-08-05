<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\Domain\Model;

use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\Mysqlreport\Domain\Model\ExplainInformation;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 */
class ExplainInformationTest extends UnitTestCase
{
    /**
     * @var ExplainInformation
     */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new ExplainInformation();
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject,
        );
    }

    #[Test]
    public function getExplainResultsInitiallyReturnsEmptyArray(): void
    {
        self::assertSame(
            [],
            $this->subject->getExplainResults(),
        );
    }

    #[Test]
    public function addExplainResultAddsExplainResult(): void
    {
        $this->subject->addExplainResult(['name' => 'Petra']);

        self::assertSame(
            [
                0 => ['name' => 'Petra'],
            ],
            $this->subject->getExplainResults(),
        );
    }

    #[Test]
    public function isQueryUsingIndexInitiallyReturnsTrue(): void
    {
        self::assertTrue(
            $this->subject->isQueryUsingIndex(),
        );
    }

    #[Test]
    public function setIsQueryUsingIndexSetsQueryUsingIndex(): void
    {
        $this->subject->setIsQueryUsingIndex(false);

        self::assertFalse(
            $this->subject->isQueryUsingIndex(),
        );
    }

    #[Test]
    public function isQueryUsingFTSInitiallyReturnsTrue(): void
    {
        self::assertFalse(
            $this->subject->isQueryUsingFTS(),
        );
    }

    #[Test]
    public function setIsQueryUsingFTSSetsQueryUsingFTS(): void
    {
        $this->subject->setIsQueryUsingFTS(true);

        self::assertTrue(
            $this->subject->isQueryUsingFTS(),
        );
    }
}

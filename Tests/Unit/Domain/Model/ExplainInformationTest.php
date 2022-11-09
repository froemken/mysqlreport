<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use StefanFroemken\Mysqlreport\Domain\Model\ExplainInformation;

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
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getExplainResultsInitiallyReturnsEmptyArray(): void
    {
        self::assertSame(
            [],
            $this->subject->getExplainResults()
        );
    }

    /**
     * @test
     */
    public function setExplainResultsSetsExplainResults(): void
    {
        $arr = [
            'foo' => 'bar'
        ];

        $this->subject->setExplainResults($arr);

        self::assertSame(
            $arr,
            $this->subject->getExplainResults()
        );
    }

    /**
     * @test
     */
    public function addExplainResultAddsExplainResult(): void
    {
        $this->subject->setExplainResults([0 => ['name' => 'Stefan']]);
        $this->subject->addExplainResult(['name' => 'Petra']);

        self::assertSame(
            [
                0 => ['name' => 'Stefan'],
                1 => ['name' => 'Petra'],
            ],
            $this->subject->getExplainResults()
        );
    }

    /**
     * @test
     */
    public function isQueryUsingIndexInitiallyReturnsTrue(): void
    {
        self::assertTrue(
            $this->subject->isQueryUsingIndex()
        );
    }

    /**
     * @test
     */
    public function setIsQueryUsingIndexSetsQueryUsingIndex(): void
    {
        $this->subject->setIsQueryUsingIndex(false);

        self::assertFalse(
            $this->subject->isQueryUsingIndex()
        );
    }

    /**
     * @test
     */
    public function isQueryUsingFTSInitiallyReturnsTrue(): void
    {
        self::assertFalse(
            $this->subject->isQueryUsingFTS()
        );
    }

    /**
     * @test
     */
    public function setIsQueryUsingFTSSetsQueryUsingFTS(): void
    {
        $this->subject->setIsQueryUsingFTS(true);

        self::assertTrue(
            $this->subject->isQueryUsingFTS()
        );
    }
}

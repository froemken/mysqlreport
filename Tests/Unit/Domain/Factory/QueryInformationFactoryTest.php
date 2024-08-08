<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\Domain\Factory;

use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\Mysqlreport\Domain\Factory\QueryInformationFactory;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 */
class QueryInformationFactoryTest extends UnitTestCase
{
    /**
     * @var QueryInformationFactory
     */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new QueryInformationFactory();
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject,
        );
    }

    #[Test]
    public function createNewQueryInformationWillCreateQueryInformationWithPid0(): void
    {
        self::assertSame(
            0,
            $this->subject->createNewQueryInformation()->getPid(),
        );
    }
}

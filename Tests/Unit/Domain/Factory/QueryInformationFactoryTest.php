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
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 */
class QueryInformationFactoryTest extends UnitTestCase
{
    private QueryInformationFactory $subject;

    protected function setUp(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://www.example.com/'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('routing', new PageArguments(1, '0', []));

        $this->subject = new QueryInformationFactory();
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject,
        );
    }

    #[Test]
    public function createNewQueryInformationWillCreateQueryInformationWithPid1(): void
    {
        self::assertSame(
            1,
            $this->subject->createNewQueryInformation()->getPid(),
        );
    }
}

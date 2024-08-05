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
use StefanFroemken\Mysqlreport\Domain\Model\Profile;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 */
class ProfileTest extends UnitTestCase
{
    /**
     * @var Profile
     */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new Profile();
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject,
        );
    }

    #[Test]
    public function getUidInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->subject->getUid(),
        );
    }

    #[Test]
    public function setUidSetsUid(): void
    {
        $this->subject->setUid(123);

        self::assertSame(
            123,
            $this->subject->getUid(),
        );
    }

    #[Test]
    public function getPidInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->subject->getPid(),
        );
    }

    #[Test]
    public function setPidSetsPid(): void
    {
        $this->subject->setPid(123);

        self::assertSame(
            123,
            $this->subject->getPid(),
        );
    }

    #[Test]
    public function getIpInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getIp(),
        );
    }

    #[Test]
    public function setIpSetsIp(): void
    {
        $this->subject->setIp('127.0.0.1');

        self::assertSame(
            '127.0.0.1',
            $this->subject->getIp(),
        );
    }

    #[Test]
    public function getRefererInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getReferer(),
        );
    }

    #[Test]
    public function setRefererSetsReferer(): void
    {
        $this->subject->setReferer('www.typo3lexikon.de');

        self::assertSame(
            'www.typo3lexikon.de',
            $this->subject->getReferer(),
        );
    }

    #[Test]
    public function getRequestInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getRequest(),
        );
    }

    #[Test]
    public function setRequestSetsRequest(): void
    {
        $this->subject->setRequest('www.typo3lexikon.de');

        self::assertSame(
            'www.typo3lexikon.de',
            $this->subject->getRequest(),
        );
    }

    #[Test]
    public function getQueryTypeInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getQueryType(),
        );
    }

    #[Test]
    public function getDurationInitiallyReturnsEmptyFloat(): void
    {
        self::assertSame(
            0.0,
            $this->subject->getDuration(),
        );
    }

    #[Test]
    public function setDurationSetsDuration(): void
    {
        $this->subject->setDuration(12.34);

        self::assertSame(
            12.34,
            $this->subject->getDuration(),
        );
    }

    #[Test]
    public function getQueryInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getQuery(),
        );
    }

    #[Test]
    public function setQuerySetsQueryAndType(): void
    {
        $this->subject->setQuery('SELECT * FROM pages;');

        self::assertSame(
            'SELECT * FROM pages;',
            $this->subject->getQuery(),
        );

        self::assertSame(
            'SELECT',
            $this->subject->getQueryType(),
        );
    }

    #[Test]
    public function getQueryParametersInitiallyReturnsEmptyArray(): void
    {
        self::assertSame(
            [],
            $this->subject->getQueryParameters(),
        );
    }

    #[Test]
    public function setQueryParametersSetsQueryParameters(): void
    {
        $this->subject->setQueryParameters([':dcValue1' => 'Stefan']);

        self::assertSame(
            [':dcValue1' => 'Stefan'],
            $this->subject->getQueryParameters(),
        );
    }

    #[Test]
    public function getQueryParameterTypesInitiallyReturnsEmptyArray(): void
    {
        self::assertSame(
            [],
            $this->subject->getQueryParameterTypes(),
        );
    }

    #[Test]
    public function setQueryParameterTypesSetsQueryParameterTypes(): void
    {
        $this->subject->setQueryParameterTypes([':dcValue1' => 'string']);

        self::assertSame(
            [':dcValue1' => 'string'],
            $this->subject->getQueryParameterTypes(),
        );
    }

    #[Test]
    public function modifyingExplainInformationModifiesExplainInformation(): void
    {
        $this->subject->getExplainInformation()->addExplainResult(['name' => 'Stefan']);

        self::assertSame(
            [
                0 => ['name' => 'Stefan'],
            ],
            $this->subject->getExplainInformation()->getExplainResults(),
        );
    }

    #[Test]
    public function getModeInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getMode(),
        );
    }

    #[Test]
    public function setModeSetsMode(): void
    {
        $this->subject->setMode('BE');

        self::assertSame(
            'BE',
            $this->subject->getMode(),
        );
    }

    #[Test]
    public function getUniqueCallIdentifierInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getUniqueCallIdentifier(),
        );
    }

    #[Test]
    public function setUniqueCallIdentifierSetsUniqueCallIdentifier(): void
    {
        $this->subject->setUniqueCallIdentifier('cn7g483ng.r4832zt');

        self::assertSame(
            'cn7g483ng.r4832zt',
            $this->subject->getUniqueCallIdentifier(),
        );
    }

    #[Test]
    public function getCrdateInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->subject->getCrdate(),
        );
    }

    #[Test]
    public function setCrdateSetsCrdate(): void
    {
        $this->subject->setCrdate(46373728);

        self::assertSame(
            46373728,
            $this->subject->getCrdate(),
        );
    }
}

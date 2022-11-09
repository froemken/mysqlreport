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
use StefanFroemken\Mysqlreport\Domain\Model\Profile;
use TYPO3\CMS\Core\Database\Connection;

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
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getUidInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->subject->getUid()
        );
    }

    /**
     * @test
     */
    public function setUidSetsUid(): void
    {
        $this->subject->setUid(123);

        self::assertSame(
            123,
            $this->subject->getUid()
        );
    }

    /**
     * @test
     */
    public function getPidInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->subject->getPid()
        );
    }

    /**
     * @test
     */
    public function setPidSetsPid(): void
    {
        $this->subject->setPid(123);

        self::assertSame(
            123,
            $this->subject->getPid()
        );
    }

    /**
     * @test
     */
    public function getIpInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getIp()
        );
    }

    /**
     * @test
     */
    public function setIpSetsIp(): void
    {
        $this->subject->setIp('127.0.0.1');

        self::assertSame(
            '127.0.0.1',
            $this->subject->getIp()
        );
    }

    /**
     * @test
     */
    public function getRefererInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getReferer()
        );
    }

    /**
     * @test
     */
    public function setRefererSetsReferer(): void
    {
        $this->subject->setReferer('www.typo3lexikon.de');

        self::assertSame(
            'www.typo3lexikon.de',
            $this->subject->getReferer()
        );
    }

    /**
     * @test
     */
    public function getRequestInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getRequest()
        );
    }

    /**
     * @test
     */
    public function setRequestSetsRequest(): void
    {
        $this->subject->setRequest('www.typo3lexikon.de');

        self::assertSame(
            'www.typo3lexikon.de',
            $this->subject->getRequest()
        );
    }

    /**
     * @test
     */
    public function getQueryTypeInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getQueryType()
        );
    }

    /**
     * @test
     */
    public function getDurationInitiallyReturnsEmptyFloat(): void
    {
        self::assertSame(
            0.0,
            $this->subject->getDuration()
        );
    }

    /**
     * @test
     */
    public function setDurationSetsDuration(): void
    {
        $this->subject->setDuration(12.34);

        self::assertSame(
            12.34,
            $this->subject->getDuration()
        );
    }

    /**
     * @test
     */
    public function getQueryInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getQuery()
        );
    }

    /**
     * @test
     */
    public function setQuerySetsQueryAndType(): void
    {
        $this->subject->setQuery('SELECT * FROM pages;');

        self::assertSame(
            'SELECT * FROM pages;',
            $this->subject->getQuery()
        );

        self::assertSame(
            'SELECT',
            $this->subject->getQueryType()
        );
    }

    public function queryDataProvider(): array
    {
        return [
            'Simple query' => [
                'SELECT * FROM pages',
                [],
                [],
                'SELECT * FROM pages'
            ],

            'Query with named int parameter' => [
                'SELECT * FROM pages WHERE crdate = :dcValue1',
                ['dcValue1' => 123],
                ['dcValue1' => \PDO::PARAM_INT],
                'SELECT * FROM pages WHERE crdate = 123'
            ],

            'Query with named bool parameter' => [
                'SELECT * FROM pages WHERE hidden = :dcValue1',
                ['dcValue1' => true],
                ['dcValue1' => \PDO::PARAM_BOOL],
                'SELECT * FROM pages WHERE hidden = 1'
            ],

            'Query with named null parameter' => [
                'SELECT * FROM pages WHERE bodytext IS :dcValue1',
                ['dcValue1' => null],
                ['dcValue1' => \PDO::PARAM_NULL],
                'SELECT * FROM pages WHERE bodytext IS NULL'
            ],

            'Query with named int array parameter' => [
                'SELECT * FROM pages WHERE pid IN (:dcValue1)',
                ['dcValue1' => [1, 3, 5]],
                ['dcValue1' => Connection::PARAM_INT_ARRAY],
                'SELECT * FROM pages WHERE pid IN (1, 3, 5)'
            ],

            'Query with named string array parameter' => [
                'SELECT * FROM pages WHERE title IN (:dcValue1)',
                ['dcValue1' => ['Stefan', 'Petra', 'Lars']],
                ['dcValue1' => Connection::PARAM_STR_ARRAY],
                'SELECT * FROM pages WHERE title IN (\'Stefan\', \'Petra\', \'Lars\')'
            ],

            'Query with named string parameter' => [
                'SELECT * FROM pages WHERE title = :dcValue1',
                ['dcValue1' => 'Stefan'],
                ['dcValue1' => \PDO::PARAM_STR],
                'SELECT * FROM pages WHERE title = \'Stefan\''
            ],

            'Query with multiple parameter' => [
                'SELECT * FROM pages WHERE title = :dcValue1 AND crdate = :dcValue2',
                ['dcValue1' => 'Stefan', 'dcValue2' => 123],
                ['dcValue1' => \PDO::PARAM_STR, 'dcValue2' => \PDO::PARAM_INT],
                'SELECT * FROM pages WHERE title = \'Stefan\' AND crdate = 123'
            ],

            'Query with questionmark as string' => [
                'DELETE FROM cache_hash WHERE identifier = ?',
                [0 => 'trh74823nthgdm8g'],
                [],
                'DELETE FROM cache_hash WHERE identifier = \'trh74823nthgdm8g\''
            ],

            'Query with questionmark as int' => [
                'DELETE FROM cache_hash WHERE uid = ?',
                [0 => 467218],
                [],
                'DELETE FROM cache_hash WHERE uid = 467218'
            ],

            'Query with questionmarks' => [
                'SELECT * FROM pages WHERE title = ? AND crdate = ?',
                [0 => 'Stefan', 1 => 123],
                [],
                'SELECT * FROM pages WHERE title = \'Stefan\' AND crdate = 123'
            ],

            'Query with multiple IN questionmarks' => [
                'SELECT * FROM pages WHERE pid IN (?, ?, ?)',
                [0 => 1, 1 => 3, 2 => 5],
                [],
                'SELECT * FROM pages WHERE pid IN (1, 3, 5)'
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider queryDataProvider
     */
    public function getQueryWithReplacedParameters(string $query, array $parameters, array $types, string $expectedQuery): void
    {
        $this->subject->setQuery($query);
        $this->subject->setQueryParameters($parameters);
        $this->subject->setQueryParameterTypes($types);

        self::assertSame(
            $expectedQuery,
            $this->subject->getQueryWithReplacedParameters()
        );
    }

    /**
     * @test
     */
    public function getQueryParametersInitiallyReturnsEmptyArray(): void
    {
        self::assertSame(
            [],
            $this->subject->getQueryParameters()
        );
    }

    /**
     * @test
     */
    public function setQueryParametersSetsQueryParameters(): void
    {
        $this->subject->setQueryParameters([':dcValue1' => 'Stefan']);

        self::assertSame(
            [':dcValue1' => 'Stefan'],
            $this->subject->getQueryParameters()
        );
    }

    /**
     * @test
     */
    public function setQueryParametersWithNullSetsQueryParameters(): void
    {
        $this->subject->setQueryParameters(null);

        self::assertSame(
            [],
            $this->subject->getQueryParameters()
        );
    }

    /**
     * @test
     */
    public function getQueryParameterTypesInitiallyReturnsEmptyArray(): void
    {
        self::assertSame(
            [],
            $this->subject->getQueryParameterTypes()
        );
    }

    /**
     * @test
     */
    public function setQueryParameterTypesSetsQueryParameterTypes(): void
    {
        $this->subject->setQueryParameterTypes([':dcValue1' => 'string']);

        self::assertSame(
            [':dcValue1' => 'string'],
            $this->subject->getQueryParameterTypes()
        );
    }

    /**
     * @test
     */
    public function getProfileInitiallyReturnsEmptyArray(): void
    {
        self::assertSame(
            [],
            $this->subject->getProfile()
        );
    }

    /**
     * @test
     */
    public function setProfileSetsProfiles(): void
    {
        $this->subject->setProfile(['name' => 'Stefan']);

        self::assertSame(
            ['name' => 'Stefan'],
            $this->subject->getProfile()
        );
    }

    /**
     * @test
     */
    public function setProfileWithNullSetsProfile(): void
    {
        $this->subject->setProfile(null);

        self::assertSame(
            [],
            $this->subject->getProfile()
        );
    }

    /**
     * @test
     */
    public function modifyingExplainInformationModifiesExplainInformation(): void
    {
        $this->subject->getExplainInformation()->setExplainResults(['name' => 'Stefan']);

        self::assertSame(
            ['name' => 'Stefan'],
            $this->subject->getExplainInformation()->getExplainResults()
        );
    }

    /**
     * @test
     */
    public function getModeInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getMode()
        );
    }

    /**
     * @test
     */
    public function setModeSetsMode(): void
    {
        $this->subject->setMode('BE');

        self::assertSame(
            'BE',
            $this->subject->getMode()
        );
    }

    /**
     * @test
     */
    public function getUniqueCallIdentifierInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getUniqueCallIdentifier()
        );
    }

    /**
     * @test
     */
    public function setUniqueCallIdentifierSetsUniqueCallIdentifier(): void
    {
        $this->subject->setUniqueCallIdentifier('cn7g483ng.r4832zt');

        self::assertSame(
            'cn7g483ng.r4832zt',
            $this->subject->getUniqueCallIdentifier()
        );
    }

    /**
     * @test
     */
    public function getCrdateInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->subject->getCrdate()
        );
    }

    /**
     * @test
     */
    public function setCrdateSetsCrdate(): void
    {
        $this->subject->setCrdate(46373728);

        self::assertSame(
            46373728,
            $this->subject->getCrdate()
        );
    }
}

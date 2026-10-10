<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\Helper;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;
use StefanFroemken\Mysqlreport\Helper\QueryParamsHelper;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class QueryParamsHelperTest extends UnitTestCase
{
    #[Test]
    public function getQueryWithReplacedParamsReturnsUnchangedSqlOnEmptyParams(): void
    {
        $helper = $this->createQueryParamsHelper();
        $sql = 'SELECT * FROM tt_content WHERE deleted = 0';

        $result = $helper->getQueryWithReplacedParams($sql, []);

        self::assertSame($sql, $result);
    }

    #[Test]
    public function getQueryWithReplacedParamsHandlesZeroIndexedPositionalParameters(): void
    {
        $helper = $this->createQueryParamsHelper();
        $sql = 'SELECT * FROM tt_content WHERE uid = ? AND pid = ?';
        $params = [0 => 42, 1 => 100];

        $result = $helper->getQueryWithReplacedParams($sql, $params);

        self::assertSame('SELECT * FROM tt_content WHERE uid = 42 AND pid = 100', $result);
    }

    #[Test]
    public function getQueryWithReplacedParamsHandlesOneIndexedPositionalParameters(): void
    {
        $helper = $this->createQueryParamsHelper();
        $sql = 'SELECT * FROM tt_content WHERE uid = ? AND pid = ?';
        $params = [1 => 42, 2 => 100];

        $result = $helper->getQueryWithReplacedParams($sql, $params);

        self::assertSame('SELECT * FROM tt_content WHERE uid = 42 AND pid = 100', $result);
    }

    #[Test]
    public function getQueryWithReplacedParamsHandlesDifferentDataTypes(): void
    {
        $helper = $this->createQueryParamsHelper();
        $sql = 'INSERT INTO tx_table (c_int, c_str, c_null, c_true, c_false) VALUES (?, ?, ?, ?, ?)';
        $params = [42, 'hello world', null, true, false];

        $result = $helper->getQueryWithReplacedParams($sql, $params);

        $expected = "INSERT INTO tx_table (c_int, c_str, c_null, c_true, c_false) VALUES (42, 'hello world', NULL, true, false)";
        self::assertSame($expected, $result);
    }

    #[Test]
    public function getQueryWithReplacedParamsAppliesExplicitDoctrineTypes(): void
    {
        $helper = $this->createQueryParamsHelper();
        $sql = 'SELECT * FROM tt_content WHERE crdate = ?';
        $params = [new \DateTime('2026-10-10 12:00:00')];
        $types = ['datetime'];

        $result = $helper->getQueryWithReplacedParams($sql, $params, $types);

        self::assertSame("SELECT * FROM tt_content WHERE crdate = '2026-10-10 12:00:00'", $result);
    }

    #[Test]
    public function getQueryWithReplacedParamsHandlesNamedParametersWithoutColons(): void
    {
        $helper = $this->createQueryParamsHelper();
        $sql = 'SELECT * FROM pages WHERE uid = :id AND title = :title';
        $params = ['id' => 10, 'title' => 'Home'];

        $result = $helper->getQueryWithReplacedParams($sql, $params);

        self::assertSame("SELECT * FROM pages WHERE uid = 10 AND title = 'Home'", $result);
    }

    #[Test]
    public function getQueryWithReplacedParamsHandlesNamedParametersWithColonsInKeys(): void
    {
        $helper = $this->createQueryParamsHelper();
        $sql = 'SELECT * FROM pages WHERE uid = :id AND title = :title';
        $params = [':id' => 10, ':title' => 'Home'];

        $result = $helper->getQueryWithReplacedParams($sql, $params);

        self::assertSame("SELECT * FROM pages WHERE uid = 10 AND title = 'Home'", $result);
    }

    #[Test]
    public function getQueryWithReplacedParamsDoesNotReplacePlaceholdersInsideStringLiterals(): void
    {
        $helper = $this->createQueryParamsHelper();
        $sql = "SELECT * FROM tt_content WHERE title = 'What? Is it: cool?' AND uid = :id AND bodytext = ?";
        $params = ['id' => 99, 0 => 'Yes!'];

        $result = $helper->getQueryWithReplacedParams($sql, $params);

        $expected = "SELECT * FROM tt_content WHERE title = 'What? Is it: cool?' AND uid = 99 AND bodytext = 'Yes!'";
        self::assertSame($expected, $result);
    }

    #[Test]
    public function getQueryWithReplacedParamsLeavesUnmatchedPlaceholdersIntact(): void
    {
        $helper = $this->createQueryParamsHelper();
        $sql = 'SELECT * FROM tt_content WHERE uid = :id AND pid = :pid AND hidden = ?';
        $params = ['id' => 5];

        $result = $helper->getQueryWithReplacedParams($sql, $params);

        self::assertSame('SELECT * FROM tt_content WHERE uid = 5 AND pid = :pid AND hidden = ?', $result);
    }

    #[Test]
    public function getQueryWithReplacedParamsLogsErrorAndReturnsOriginalSqlOnException(): void
    {
        $exception = new \RuntimeException('Database platform unavailable');

        $connectionPool = $this->createMock(ConnectionPool::class);
        $connectionPool->expects($this->once())
            ->method('getConnectionByName')
            ->willThrowException($exception);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with(
                'Error while replacing placeholders in query',
                ['exception' => $exception],
            );

        $helper = new QueryParamsHelper($connectionPool, $logger);
        $sql = 'SELECT * FROM tt_content WHERE uid = ?';

        $result = $helper->getQueryWithReplacedParams($sql, [1]);

        self::assertSame($sql, $result);
    }

    private function createQueryParamsHelper(?LoggerInterface $logger = null): QueryParamsHelper
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn(new MySQLPlatform());

        $connectionPool = $this->createMock(ConnectionPool::class);
        $connectionPool->method('getConnectionByName')
            ->with(ConnectionPool::DEFAULT_CONNECTION_NAME)
            ->willReturn($connection);

        return new QueryParamsHelper(
            $connectionPool,
            $logger ?? $this->createMock(LoggerInterface::class),
        );
    }
}

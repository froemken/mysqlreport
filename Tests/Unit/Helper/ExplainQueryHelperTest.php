<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\Helper;

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Result;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;
use StefanFroemken\Mysqlreport\Configuration\ExtConf;
use StefanFroemken\Mysqlreport\Domain\Model\QueryInformation;
use StefanFroemken\Mysqlreport\Helper\ExplainQueryHelper;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ExplainQueryHelperTest extends UnitTestCase
{
    #[Test]
    public function updateQueryInformationReturnsEarlyWhenExplainIsDeactivated(): void
    {
        $connectionPool = $this->createMock(ConnectionPool::class);
        $connectionPool->expects($this->never())->method('getConnectionByName');

        $extConf = new ExtConf(activateExplainQuery: false);

        $logger = $this->createMock(LoggerInterface::class);

        $helper = new ExplainQueryHelper($connectionPool, $extConf, $logger);

        $queryInformation = new QueryInformation();
        $queryInformation->setQuery('SELECT * FROM tt_content');

        $helper->updateQueryInformation($queryInformation);

        self::assertTrue($queryInformation->getExplainInformation()->isQueryUsingIndex());
        self::assertFalse($queryInformation->getExplainInformation()->isQueryUsingFTS());
    }

    #[Test]
    public function updateQueryInformationReturnsEarlyWhenQueryTypeIsNotSelect(): void
    {
        $connectionPool = $this->createMock(ConnectionPool::class);
        $connectionPool->expects($this->never())->method('getConnectionByName');

        $extConf = new ExtConf(activateExplainQuery: true);

        $logger = $this->createMock(LoggerInterface::class);

        $helper = new ExplainQueryHelper($connectionPool, $extConf, $logger);

        $queryInformation = new QueryInformation();
        $queryInformation->setQuery('UPDATE tt_content SET header = "foo"');

        $helper->updateQueryInformation($queryInformation);

        self::assertTrue($queryInformation->getExplainInformation()->isQueryUsingIndex());
        self::assertFalse($queryInformation->getExplainInformation()->isQueryUsingFTS());
    }

    #[Test]
    public function updateQueryInformationParsesExplainRowsCorrectly(): void
    {
        $result = $this->createMock(Result::class);
        $result->expects($this->exactly(3))
            ->method('fetchAssociative')
            ->willReturnOnConsecutiveCalls(
                [
                    'id' => '1',
                    'select_type' => 'SIMPLE',
                    'table' => 'tt_content',
                    'type' => 'ALL',
                    'key' => null,
                ],
                [
                    'id' => '2',
                    'select_type' => 'SIMPLE',
                    'table' => 'pages',
                    'type' => 'eq_ref',
                    'key' => 'PRIMARY',
                ],
                false,
            );

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('executeQuery')
            ->with('EXPLAIN SELECT * FROM tt_content JOIN pages ON tt_content.pid = pages.uid')
            ->willReturn($result);

        $connectionPool = $this->createMock(ConnectionPool::class);
        $connectionPool->expects($this->once())
            ->method('getConnectionByName')
            ->with(ConnectionPool::DEFAULT_CONNECTION_NAME)
            ->willReturn($connection);

        $extConf = new ExtConf(activateExplainQuery: true);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('error');

        $helper = new ExplainQueryHelper($connectionPool, $extConf, $logger);

        $queryInformation = new QueryInformation();
        $queryInformation->setQuery('SELECT * FROM tt_content JOIN pages ON tt_content.pid = pages.uid');

        $helper->updateQueryInformation($queryInformation);

        $explain = $queryInformation->getExplainInformation();
        self::assertCount(2, $explain->getExplainResults());
        self::assertFalse($explain->isQueryUsingIndex());
        self::assertTrue($explain->isQueryUsingFTS());
    }

    #[Test]
    public function updateQueryInformationLogsErrorOnDatabaseException(): void
    {
        $exception = $this->createMock(DriverException::class);

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('executeQuery')
            ->willThrowException($exception);

        $connectionPool = $this->createMock(ConnectionPool::class);
        $connectionPool->expects($this->once())
            ->method('getConnectionByName')
            ->with(ConnectionPool::DEFAULT_CONNECTION_NAME)
            ->willReturn($connection);

        $extConf = new ExtConf(activateExplainQuery: true);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with(
                'Error while executing EXPLAIN query',
                ['exception' => $exception],
            );

        $helper = new ExplainQueryHelper($connectionPool, $extConf, $logger);

        $queryInformation = new QueryInformation();
        $queryInformation->setQuery('SELECT * FROM non_existent_table');

        $helper->updateQueryInformation($queryInformation);

        self::assertEmpty($queryInformation->getExplainInformation()->getExplainResults());
    }
}

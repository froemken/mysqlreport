<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\Domain\Repository;

use Doctrine\DBAL\Exception\DriverException;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;
use StefanFroemken\Mysqlreport\Configuration\ExtConf;
use StefanFroemken\Mysqlreport\Domain\Repository\QueryInformationRepository;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class QueryInformationRepositoryTest extends UnitTestCase
{
    #[Test]
    public function bulkInsertStoresRecordsInChunksOfFifty(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->exactly(2))
            ->method('bulkInsert')
            ->with(
                'tx_mysqlreport_query_information',
                self::isType('array'),
                self::isType('array'),
            );

        $connectionPool = $this->createMock(ConnectionPool::class);
        $connectionPool->expects($this->once())
            ->method('getConnectionForTable')
            ->with('tx_mysqlreport_query_information')
            ->willReturn($connection);

        $extConf = new ExtConf();
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('error');

        $repository = new QueryInformationRepository(
            $connectionPool,
            $extConf,
            $logger,
        );

        $queries = [];
        for ($i = 0; $i < 65; $i++) {
            $queries[] = [
                'pid' => 1,
                'query' => 'SELECT ' . $i,
            ];
        }

        $repository->bulkInsert($queries);
    }

    #[Test]
    public function bulkInsertLogsErrorOnDatabaseException(): void
    {
        $exception = $this->createMock(DriverException::class);

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('bulkInsert')
            ->willThrowException($exception);

        $connectionPool = $this->createMock(ConnectionPool::class);
        $connectionPool->expects($this->once())
            ->method('getConnectionForTable')
            ->with('tx_mysqlreport_query_information')
            ->willReturn($connection);

        $extConf = new ExtConf();
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with(
                'Error while bulk inserting query information records',
                ['exception' => $exception],
            );

        $repository = new QueryInformationRepository(
            $connectionPool,
            $extConf,
            $logger,
        );

        $repository->bulkInsert([['pid' => 1, 'query' => 'SELECT 1']]);
    }
}

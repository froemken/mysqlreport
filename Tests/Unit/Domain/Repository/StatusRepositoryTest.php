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
use Doctrine\DBAL\Result;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;
use StefanFroemken\Mysqlreport\Domain\Repository\StatusRepository;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class StatusRepositoryTest extends UnitTestCase
{
    #[Test]
    public function findAllReturnsStatusValuesFromGlobalStatus(): void
    {
        $result = $this->createMock(Result::class);
        $result->expects($this->exactly(3))
            ->method('fetchAssociative')
            ->willReturnOnConsecutiveCalls(
                ['Variable_name' => 'Uptime', 'Value' => '3600'],
                ['Variable_name' => 'Questions', 'Value' => '1000'],
                false,
            );

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('executeQuery')
            ->with('SHOW GLOBAL STATUS')
            ->willReturn($result);

        $connectionPool = $this->createMock(ConnectionPool::class);
        $connectionPool->expects($this->once())
            ->method('getConnectionByName')
            ->with(ConnectionPool::DEFAULT_CONNECTION_NAME)
            ->willReturn($connection);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('error');

        $repository = new StatusRepository($connectionPool, $logger);
        $statusValues = $repository->findAll();

        self::assertSame('3600', $statusValues['Uptime']);
        self::assertSame('1000', $statusValues['Questions']);
    }

    #[Test]
    public function findAllLogsErrorOnDatabaseExceptionAndReturnsEmptyStatusValues(): void
    {
        $exception = $this->createMock(DriverException::class);

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('executeQuery')
            ->with('SHOW GLOBAL STATUS')
            ->willThrowException($exception);

        $connectionPool = $this->createMock(ConnectionPool::class);
        $connectionPool->expects($this->once())
            ->method('getConnectionByName')
            ->with(ConnectionPool::DEFAULT_CONNECTION_NAME)
            ->willReturn($connection);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with(
                'Error while retrieving status values from database server',
                ['exception' => $exception],
            );

        $repository = new StatusRepository($connectionPool, $logger);
        $statusValues = $repository->findAll();

        self::assertNull($statusValues['Uptime']);
    }
}

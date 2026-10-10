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
use StefanFroemken\Mysqlreport\Domain\Repository\VariablesRepository;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class VariablesRepositoryTest extends UnitTestCase
{
    #[Test]
    public function findAllReturnsVariablesFromGlobalVariables(): void
    {
        $result = $this->createMock(Result::class);
        $result->expects($this->exactly(3))
            ->method('fetchAssociative')
            ->willReturnOnConsecutiveCalls(
                ['Variable_name' => 'max_connections', 'Value' => '151'],
                ['Variable_name' => 'query_cache_size', 'Value' => '1048576'],
                false,
            );

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('executeQuery')
            ->with('SHOW GLOBAL VARIABLES')
            ->willReturn($result);

        $connectionPool = $this->createMock(ConnectionPool::class);
        $connectionPool->expects($this->once())
            ->method('getConnectionByName')
            ->with(ConnectionPool::DEFAULT_CONNECTION_NAME)
            ->willReturn($connection);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('error');

        $repository = new VariablesRepository($connectionPool, $logger);
        $variables = $repository->findAll();

        self::assertSame('151', $variables['max_connections']);
        self::assertSame('1048576', $variables['query_cache_size']);
    }

    #[Test]
    public function findAllLogsErrorOnDatabaseExceptionAndReturnsEmptyVariables(): void
    {
        $exception = $this->createMock(DriverException::class);

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('executeQuery')
            ->with('SHOW GLOBAL VARIABLES')
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
                'Error while retrieving variables from database server',
                ['exception' => $exception],
            );

        $repository = new VariablesRepository($connectionPool, $logger);
        $variables = $repository->findAll();

        self::assertNull($variables['max_connections']);
    }
}

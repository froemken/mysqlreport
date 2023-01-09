<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Helper;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Result;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * Helper to wrap the executeQuery method.
 * Needed to temporarily deactivate the SQL logger
 */
class ConnectionHelper
{
    private ?Connection $connection = null;

    private ConnectionPool $connectionPool;

    private SqlLoggerHelper $sqlLoggerHelper;

    public function injectConnectionPool(ConnectionPool $connectionPool): void
    {
        $this->connectionPool = $connectionPool;
        $this->connection = $this->getConnection();
    }

    public function injectSqlLoggerHelper(SqlLoggerHelper $sqlLoggerHelper): void
    {
        $this->sqlLoggerHelper = $sqlLoggerHelper;
        $this->sqlLoggerHelper->setConnectionConfiguration($this->connection->getConfiguration());
    }

    /**
     * Executes a query which will not be logged by our SQL logger
     */
    public function executeQuery(string $query): ?Result
    {
        if (!$this->isConnectionAvailable()) {
            return null;
        }

        $currentSqlLogger = $this->sqlLoggerHelper->getCurrentSqlLogger();
        $this->sqlLoggerHelper->deactivateSqlLogger();

        try {
            $result = $this->connection->executeQuery($query);
        } catch (Exception $exception) {
            $result = null;
        }

        $this->sqlLoggerHelper->activateSqlLogger($currentSqlLogger);

        return $result;
    }

    /**
     * Executes a bulk insert which will not be logged by our SQL logger
     */
    public function bulkInsert(string $tableName, array $data, array $columns = [], array $types = []): int
    {
        if (!$this->isConnectionAvailable()) {
            return 0;
        }

        $currentSqlLogger = $this->sqlLoggerHelper->getCurrentSqlLogger();
        $this->sqlLoggerHelper->deactivateSqlLogger();

        $affectedRows = $this->connection->bulkInsert($tableName, $data, $columns, $types);

        $this->sqlLoggerHelper->activateSqlLogger($currentSqlLogger);

        return $affectedRows;
    }

    public function quote(string $value): string
    {
        if (!$this->isConnectionAvailable()) {
            return '';
        }

        return $this->connection->quote($value);
    }

    public function getConnectionConfiguration(): ?Configuration
    {
        if (!$this->isConnectionAvailable()) {
            return null;
        }

        return $this->connection->getConfiguration();
    }

    public function isConnectionAvailable(): bool
    {
        return $this->connection instanceof Connection;
    }

    public function getQueryBuilderForTable(string $table): QueryBuilder
    {
        return $this->connectionPool->getQueryBuilderForTable($table);
    }

    public function executeQueryBuilder(QueryBuilder $queryBuilder): Result
    {
        return $queryBuilder->execute();
    }

    private function getConnection(): ?Connection
    {
        try {
            return $this->connectionPool->getConnectionByName(
                ConnectionPool::DEFAULT_CONNECTION_NAME
            );
        } catch (\UnexpectedValueException $unexpectedValueException) {
            // Should never be thrown, as a hard-coded name was added as parameter
        } catch (\RuntimeException $runtimeException) {
            // Default database of TYPO3 is not configured
        } catch (Exception $exception) {
            // Something breaks in DriverManager of Doctrine DBAL
        }

        return null;
    }
}

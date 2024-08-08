<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Traits;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Trait to get TYPO3 connection pool and db connection object
 */
trait DatabaseConnectionTrait
{
    private function getConnectionPool(): ConnectionPool
    {
        return GeneralUtility::makeInstance(ConnectionPool::class);
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->getConnectionPool()->getQueryBuilderForTable('tx_mysqlreport_query_information');
    }

    private function getDefaultConnection(): Connection
    {
        return $this->getConnectionPool()->getConnectionByName(
            ConnectionPool::DEFAULT_CONNECTION_NAME,
        );
    }

    private function isValidConnectionDriver(string $connectionDriver): bool
    {
        return in_array($connectionDriver, ['mysqli', 'pdo_mysql'], true);
    }
}

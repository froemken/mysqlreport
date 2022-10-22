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
use Doctrine\DBAL\Logging\SQLLogger;
use StefanFroemken\Mysqlreport\Logger\MySqlReportSqlLogger;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Helper with useful methods for SQL logger
 */
class SqlLoggerHelper
{
    public function activateSqlLogger(?Connection $connection, SQLLogger $sqlLogger = null): void
    {
        if (
            $connection instanceof Connection
            && ($configuration = $connection->getConfiguration())
            && $configuration instanceof Configuration
        ) {
            if ($sqlLogger === null) {
                $sqlLogger = $this->getSqlLogger();
            }
            $configuration->setSQLLogger($sqlLogger);
        }
    }

    public function deactivateSqlLogger(?Connection $connection): void
    {
        if (
            $connection instanceof Connection
            && ($configuration = $connection->getConfiguration())
            && $configuration instanceof Configuration
        ) {
            $configuration->setSQLLogger();
        }
    }

    public function getCurrentSqlLogger(?Connection $connection): ?SQLLogger
    {
        if (
            $connection instanceof Connection
            && ($configuration = $connection->getConfiguration())
            && $configuration instanceof Configuration
        ) {
            return $configuration->getSQLLogger();
        }

        return null;
    }

    private function getSqlLogger(): SQLLogger
    {
        return GeneralUtility::makeInstance(MySqlReportSqlLogger::class);
    }
}

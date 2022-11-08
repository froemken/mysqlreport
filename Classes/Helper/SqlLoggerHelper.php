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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Helper with useful methods for SQL logger
 */
class SqlLoggerHelper
{
    /**
     * @var Configuration|null
     */
    private $configuration;

    public function setConnectionConfiguration(?Configuration $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function activateSqlLogger(SQLLogger $sqlLogger = null): void
    {
        if ($configuration = $this->configuration) {
            if ($sqlLogger === null) {
                $sqlLogger = $this->getSqlLogger();
            }

            $configuration->setSQLLogger($sqlLogger);
        }
    }

    public function deactivateSqlLogger(): void
    {
        if ($configuration = $this->configuration) {
            $configuration->setSQLLogger();
        }
    }

    public function getCurrentSqlLogger(): ?SQLLogger
    {
        if ($configuration = $this->configuration) {
            return $configuration->getSQLLogger();
        }

        return null;
    }

    private function getSqlLogger(): SQLLogger
    {
        return GeneralUtility::makeInstance(MySqlReportSqlLogger::class);
    }
}

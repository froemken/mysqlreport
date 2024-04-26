<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Doctrine\Middleware;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Middleware\AbstractConnectionMiddleware;
use Doctrine\DBAL\Driver\Result;
use StefanFroemken\Mysqlreport\Configuration\ExtConf;
use StefanFroemken\Mysqlreport\Domain\Factory\ProfileFactory;
use StefanFroemken\Mysqlreport\Logger\MySqlReportSqlLogger;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * Here in the connection, we can wrap our logger around the queries and commands.
 */
class LoggerWithQueryTimeConnection extends AbstractConnectionMiddleware
{
    private MySqlReportSqlLogger $logger;

    public function __construct(Connection $connection)
    {
        parent::__construct($connection);

        // As this logger is also valid for InstallTool where we have a reduced set of DI classes, we instantiate the
        // MySQL report logger on our own.
        $this->logger = new MySqlReportSqlLogger(
            new ProfileFactory(),
            new ExtConf(new ExtensionConfiguration())
        );
    }

    /**
     * This method will be called during SELECT queries
     */
    public function query(string $sql): Result
    {
        $this->logger->startQuery();
        $queryResult = parent::query($sql);
        $this->logger->stopQuery($sql);

        return $queryResult;
    }

    /**
     * This method will be called during INSERT/UPDATE/DELETE queries
     */
    public function exec(string $sql): int
    {
        $this->logger->startQuery();
        $affectedRows = parent::exec($sql);
        $this->logger->stopQuery($sql);

        return (int)$affectedRows;
    }
}

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
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Driver\Middleware\AbstractConnectionMiddleware;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use StefanFroemken\Mysqlreport\Configuration\ExtConf;
use StefanFroemken\Mysqlreport\Domain\Factory\ProfileFactory;
use StefanFroemken\Mysqlreport\Helper\ExplainQueryHelper;
use StefanFroemken\Mysqlreport\Helper\QueryParamsHelper;
use StefanFroemken\Mysqlreport\Logger\MySqlReportSqlLogger;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\ConnectionPool;

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
            new ExtConf(new ExtensionConfiguration()),
            new QueryParamsHelper(new ConnectionPool()),
            new ExplainQueryHelper(),
        );
    }

    /**
     * Prepares a statement for execution and returns a Statement object.
     *
     * @throws Exception
     */
    public function prepare(string $sql): Statement
    {
        return new LoggerStatement(parent::prepare($sql), $this->logger, $sql);
    }

    /**
     * This method will be called during SELECT queries
     */
    public function query(string $sql): Result
    {
        $startTime = microtime(true);
        $queryResult = parent::query($sql);
        $this->logger->stopQuery($sql, microtime(true) - $startTime);

        return $queryResult;
    }

    /**
     * This method will be called during INSERT/UPDATE/DELETE queries
     */
    public function exec(string $sql): int
    {
        $startTime = microtime(true);
        $affectedRows = parent::exec($sql);
        $this->logger->stopQuery($sql, microtime(true) - $startTime);

        return (int)$affectedRows;
    }
}

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
use StefanFroemken\Mysqlreport\Domain\Model\QueryInformation;
use StefanFroemken\Mysqlreport\Logger\MySqlReportSqlLogger;
use StefanFroemken\Mysqlreport\Service\QueryCollector;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Here in the connection, we can wrap our logger around the queries and commands.
 */
class LoggerWithQueryTimeConnection extends AbstractConnectionMiddleware
{
    private readonly MySqlReportSqlLogger $logger;

    private readonly QueryCollector $queryCollector;

    public function __construct(Connection $connection)
    {
        parent::__construct($connection);

        // Since Doctrine driver instances are instantiated by DBAL and not by the DI container,
        // services are resolved via GeneralUtility::makeInstance.
        $this->logger = GeneralUtility::makeInstance(MySqlReportSqlLogger::class);
        $this->queryCollector = GeneralUtility::makeInstance(QueryCollector::class);
    }

    /**
     * Prepares a statement for execution and returns a Statement object.
     *
     * @throws Exception
     */
    public function prepare(string $sql): Statement
    {
        return new LoggerStatement(
            parent::prepare($sql),
            $this->logger,
            $sql,
            $this->queryCollector,
        );
    }

    /**
     * This method will be called during SELECT queries
     *
     * @throws Exception
     */
    public function query(string $sql): Result
    {
        $startTime = microtime(true);
        $queryResult = parent::query($sql);

        if ($this->queryCollector->isFlushing()) {
            return $queryResult;
        }

        $queryInformation = $this->logger->stopQuery($sql, microtime(true) - $startTime);
        if ($queryInformation instanceof QueryInformation) {
            $this->queryCollector->addQuery($queryInformation);
        }

        return $queryResult;
    }

    /**
     * This method will be called during INSERT/UPDATE/DELETE queries
     *
     * @throws Exception
     */
    public function exec(string $sql): int
    {
        $startTime = microtime(true);
        $affectedRows = parent::exec($sql);

        if ($this->queryCollector->isFlushing()) {
            return (int)$affectedRows;
        }

        $queryInformation = $this->logger->stopQuery($sql, microtime(true) - $startTime);
        if ($queryInformation instanceof QueryInformation) {
            $this->queryCollector->addQuery($queryInformation);
        }

        return (int)$affectedRows;
    }
}

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
use StefanFroemken\Mysqlreport\Domain\Repository\QueryInformationRepository;
use StefanFroemken\Mysqlreport\Helper\ExplainQueryHelper;
use StefanFroemken\Mysqlreport\Logger\MySqlReportSqlLogger;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Here in the connection, we can wrap our logger around the queries and commands.
 */
class LoggerWithQueryTimeConnection extends AbstractConnectionMiddleware
{
    /**
     * @var \SplQueue<QueryInformation>
     */
    private \SplQueue $queries;

    private MySqlReportSqlLogger $logger;

    private ExplainQueryHelper $explainQueryHelper;

    private QueryInformationRepository $queryInformationRepository;

    public function __construct(Connection $connection)
    {
        parent::__construct($connection);

        $this->queries = new \SplQueue();
        $this->logger = GeneralUtility::makeInstance(MySqlReportSqlLogger::class);
        $this->explainQueryHelper = GeneralUtility::makeInstance(ExplainQueryHelper::class);
        $this->queryInformationRepository = GeneralUtility::makeInstance(QueryInformationRepository::class);
    }

    /**
     * Prepares a statement for execution and returns a Statement object.
     *
     * @throws Exception
     */
    public function prepare(string $sql): Statement
    {
        return GeneralUtility::makeInstance(
            LoggerStatement::class,
            parent::prepare($sql),
            $this->logger,
            $sql,
            $this->queries,
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
        $queryInformation = $this->logger->stopQuery($sql, microtime(true) - $startTime);

        if ($queryInformation instanceof QueryInformation) {
            $this->queries->push($queryInformation);
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
        $queryInformation = $this->logger->stopQuery($sql, microtime(true) - $startTime);

        if ($queryInformation instanceof QueryInformation) {
            $this->queries->push($queryInformation);
        }

        return (int)$affectedRows;
    }

    public function __destruct()
    {
        $queriesToStore = [];
        foreach ($this->queries as $index => $queryInformation) {
            $queryInformation->setQueryId($index);
            $this->explainQueryHelper->updateQueryInformation($queryInformation);

            $queriesToStore[] = $queryInformation->asArray();
        }

        $this->queryInformationRepository->bulkInsert($queriesToStore);
    }
}

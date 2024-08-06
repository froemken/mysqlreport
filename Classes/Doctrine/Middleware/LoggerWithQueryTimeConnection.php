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

    private ExtConf $extConf;

    public function __construct(Connection $connection)
    {
        parent::__construct($connection);

        $this->extConf = new ExtConf(new ExtensionConfiguration());

        // As this logger is also valid for InstallTool where we have a reduced set of DI classes, we instantiate the
        // MySQL report logger on our own.
        $this->logger = new MySqlReportSqlLogger(
            new ProfileFactory(),
            $this->extConf,
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

    public function __destruct()
    {
        $defaultConnection = $this->getTypo3DefaultConnection();
        $executeExplainQuery = $this->extConf->isAddExplain() && $defaultConnection instanceof \TYPO3\CMS\Core\Database\Connection;

        $queriesToStore = [];
        foreach ($this->profiles as $key => $profile) {
            if ($executeExplainQuery && $profile->getQueryType() === 'SELECT') {
                try {
                    $queryResult = $defaultConnection->executeQuery('EXPLAIN ' . $profile->getQuery());
                    while ($explainRow = $queryResult->fetchAssociative()) {
                        $this->explainQueryHelper->updateProfile($profile, $explainRow);
                    }
                } catch (\Doctrine\DBAL\Exception $e) {
                    continue;
                }
            }

            $queryToStore = [
                'pid' => $profile->getPid(),
                'ip' => $profile->getIp(),
                'referer' => $profile->getReferer(),
                'request' => $profile->getRequest(),
                'query_type' => $profile->getQueryType(),
                'duration' => $profile->getDuration(),
                'query' => $profile->getQuery(),
                'explain_query' => serialize($profile->getExplainInformation()->getExplainResults()),
                'using_index' => $profile->getExplainInformation()->isQueryUsingIndex() ? 1 : 0,
                'using_fulltable' => $profile->getExplainInformation()->isQueryUsingFTS() ? 1 : 0,
                'mode' => $profile->getMode(),
                'unique_call_identifier' => $profile->getUniqueCallIdentifier(),
                'crdate' => $profile->getCrdate(),
                'query_id' => $key,
            ];

            $queriesToStore[] = $queryToStore;
        }

        foreach (array_chunk($queriesToStore, 50) as $chunkOfQueriesToStore) {
            $this->bulkInsert(
                $chunkOfQueriesToStore,
                [
                    'pid',
                    'ip',
                    'referer',
                    'request',
                    'query_type',
                    'duration',
                    'query',
                    'explain_query',
                    'using_index',
                    'using_fulltable',
                    'mode',
                    'unique_call_identifier',
                    'crdate',
                    'query_id',
                ],
            );
        }
    }
}

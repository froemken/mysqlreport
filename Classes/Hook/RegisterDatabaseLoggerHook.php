<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Hook;

use Doctrine\DBAL\Logging\DebugStack;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\TableConfigurationPostProcessingHookInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Add Logger to database connection to store queries of a request
 */
class RegisterDatabaseLoggerHook implements SingletonInterface, TableConfigurationPostProcessingHookInterface
{
    /**
     * @var array
     */
    protected $extConf = [];

    public function __construct()
    {
        $this->extConf = is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mysqlreport']) ?: unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mysqlreport']);
    }

    public function processData()
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        // @ToDo: Loop through all Connection names
        $connection = $connectionPool->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        $connection->getConfiguration()->setSQLLogger(
            GeneralUtility::makeInstance(DebugStack::class)
        );
    }

    public function __destruct()
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $connection = $connectionPool->getConnectionForTable('tx_mysqlreport_domain_model_profile');

        // do not log our insert queries
        $sqlLogger = clone $connection->getConfiguration()->getSQLLogger();
        $connection->getConfiguration()->setSQLLogger(null);

        // A page can be called multiple times each second. So we need an unique identifier.
        $uniqueIdentifier = uniqid();
        $pid = is_object($GLOBALS['TSFE']) ? $GLOBALS['TSFE']->id : 0;

        if ($sqlLogger instanceof DebugStack) {
            $queriesToStore = [];
            foreach ($sqlLogger->queries as $key => $loggedQuery) {
                $queryToStore = [
                    'pid' => $pid,
                    'ip' => GeneralUtility::getIndpEnv('REMOTE_ADDR'),
                    'referer' => GeneralUtility::getIndpEnv('HTTP_REFERER'),
                    'request' => GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'),
                    'query_type' => GeneralUtility::trimExplode(' ', $loggedQuery['sql'], true, 2)[0],
                    'duration' => $loggedQuery['executionMS'],
                    'query' => $connection->quote($loggedQuery['sql']),
                    'profile' => serialize([]),
                    'explain_query' => serialize([]),
                    'not_using_index' => 0,
                    'using_fulltable' => 0,
                    'mode' => (string)TYPO3_MODE,
                    'unique_call_identifier' => $uniqueIdentifier,
                    'crdate' => (int)$GLOBALS['EXEC_TIME'],
                    'query_id' => $key
                ];

                $this->addExplainInformation($queryToStore, $loggedQuery);

                $queriesToStore[] = $queryToStore;
            }

            foreach (array_chunk($queriesToStore, 50) as $chunkOfQueriesToStore) {
                $connection->bulkInsert(
                    'tx_mysqlreport_domain_model_profile',
                    $chunkOfQueriesToStore,
                    [
                        'pid',
                        'ip',
                        'referer',
                        'request',
                        'query_type',
                        'duration',
                        'query',
                        'profile',
                        'explain_query',
                        'not_using_index',
                        'using_fulltable',
                        'mode',
                        'unique_call_identifier',
                        'crdate',
                        'query_id'
                    ]
                );
            }
        }
    }

    protected function addExplainInformation(array &$queryToStore, array $loggedQuery)
    {
        $sql = $loggedQuery['sql'];
        $queryType = GeneralUtility::trimExplode(' ', $sql, true, 2)[0];

        // save explain information of query if activated
        // EXPLAIN with other types than SELECT works since MySQL 5.6.3 only
        // EXPLAIN does not work, if we have PreparedStatements (Statements with ?)
        if (
            $this->extConf['addExplain'] &&
            strtoupper($queryType) === 'SELECT'
        ) {
            $explain = [];
            $notUsingIndex = false;
            $usingFullTable = false;

            /** @var ConnectionPool $connectionPool */
            $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
            $connection = $connectionPool->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
            $statement = $connection->query($this->buildExplainQuery($sql, $loggedQuery['params'], $loggedQuery['types']));
            while ($explainRow = $statement->fetch()) {
                if ($notUsingIndex === false && empty($explainRow['key'])) {
                    $notUsingIndex = true;
                }
                if ($usingFullTable === false && strtolower($explainRow['select_type']) === 'all') {
                    $usingFullTable = true;
                }
                $explain[] = $explainRow;
            }
            $queryToStore['explain_query'] = serialize($explain);
            $queryToStore['not_using_index'] = (int)$notUsingIndex;
            $queryToStore['using_fulltable'] = (int)$usingFullTable;
        }
    }

    protected function buildExplainQuery(string $sql, array $params, array $types)
    {
        $namedParameters = [];
        foreach ($params as $key => $param) {
            switch ($types[$key]) {
                case \PDO::PARAM_INT:
                    $param = (int)$param;
                    break;
                case \PDO::PARAM_BOOL:
                    $param = $param === true ? 1 : 0;
                    break;
                case \PDO::PARAM_NULL:
                    $param = null;
                    break;
                case Connection::PARAM_INT_ARRAY:
                    $param = implode(',', $param);
                    break;
                case Connection::PARAM_STR_ARRAY:
                    $param = '\'' . implode(',', $param) . '\'';
                    break;
                default:
                case \PDO::PARAM_STR:
                    $param = '\'' . (string)$param . '\'';
            }
            $namedParameters[':' . $key] = $param;
        }

        return 'EXPLAIN ' . str_replace(
            array_keys($namedParameters),
            $namedParameters,
            $sql
        );
    }
}

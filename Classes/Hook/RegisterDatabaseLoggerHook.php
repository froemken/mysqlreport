<?php
namespace StefanFroemken\Mysqlreport\Hook;

/*
 * This file is part of the mysqlreport project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

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
    protected $extConf = array();

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

    /**
     * add result of EXPLAIN to profiling
     *
     * @param array $queryToStore
     * @param array $loggedQuery
     * @return void
     */
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
            $explain = array();
            $notUsingIndex = FALSE;
            $usingFullTable = FALSE;

            /** @var ConnectionPool $connectionPool */
            $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
            $connection = $connectionPool->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
            $statement = $connection->query($this->buildExplainQuery($sql, $loggedQuery['params'], $loggedQuery['types']));
            while ($explainRow = $statement->fetch()) {
                if ($notUsingIndex === FALSE && empty($explainRow['key'])) {
                    $notUsingIndex = TRUE;
                }
                if ($usingFullTable === FALSE && strtolower($explainRow['select_type']) === 'all') {
                    $usingFullTable = TRUE;
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
                    $param = NULL;
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

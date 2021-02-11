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
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\TableConfigurationPostProcessingHookInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Add Logger to database connection to store queries of a request
 */
class RegisterDatabaseLoggerHook implements SingletonInterface, TableConfigurationPostProcessingHookInterface
{
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
                $queriesToStore[] = [
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
}

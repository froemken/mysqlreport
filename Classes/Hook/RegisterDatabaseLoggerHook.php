<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Hook;

use Doctrine\DBAL\Exception;
use StefanFroemken\Mysqlreport\Helper\SqlLoggerHelper;
use StefanFroemken\Mysqlreport\Logger\MySqlReportSqlLogger;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\TableConfigurationPostProcessingHookInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Add Logger to database connection to store queries of a request
 *
 * Currently, this is the earliest hook I could found in TYPO3 universe.
 * All queries executed before this hook were not collected.
 */
class RegisterDatabaseLoggerHook implements SingletonInterface, TableConfigurationPostProcessingHookInterface
{
    /**
     * @var array
     */
    protected $extConf = [];

    /**
     * @var SqlLoggerHelper
     */
    protected $sqlLoggerHelper;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * Do not add any parameters to this constructor.
     * This class was called too early for DI. BE breaks, if you try to Flush Cache in InstallTool
     */
    public function __construct()
    {
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        try {
            $this->extConf = (array)$extensionConfiguration->get('mysqlreport');
        } catch (ExtensionConfigurationExtensionNotConfiguredException | ExtensionConfigurationPathDoesNotExistException $exception) {
            $this->extConf = [];
        }

        $this->sqlLoggerHelper = GeneralUtility::makeInstance(SqlLoggerHelper::class);
        $this->connection = $this->getConnection();
    }

    public function processData(): void
    {
        if (!$this->connection instanceof Connection) {
            return;
        }

        if (
            (isset($this->extConf['profileFrontend']) && $this->extConf['profileFrontend'] && TYPO3_MODE === 'FE')
            || (isset($this->extConf['profileBackend']) && $this->extConf['profileBackend'] && TYPO3_MODE === 'BE')
        ) {
            $this->sqlLoggerHelper->activateSqlLogger($this->connection);
        }
    }

    public function __destruct()
    {
        if (!$this->connection instanceof Connection) {
            return;
        }

        if (
            (isset($this->extConf['profileFrontend']) && $this->extConf['profileFrontend'] && TYPO3_MODE === 'FE') ||
            (isset($this->extConf['profileBackend']) && $this->extConf['profileBackend'] && TYPO3_MODE === 'BE')
        ) {
            // Do not log our insert queries
            $sqlLogger = $this->sqlLoggerHelper->getCurrentSqlLogger($this->connection);
            $this->sqlLoggerHelper->deactivateSqlLogger($this->connection);

            // A page can be called multiple times each second. So we need an unique identifier.
            $uniqueIdentifier = uniqid('', true);
            $pid = isset($GLOBALS['TSFE']) && $GLOBALS['TSFE'] instanceof TypoScriptFrontendController
                ? $GLOBALS['TSFE']->id
                : 0;

            if ($sqlLogger instanceof MySqlReportSqlLogger) {
                $queriesToStore = [];
                foreach ($sqlLogger->queries as $key => $loggedQuery) {
                    $queryToStore = [
                        'pid' => $pid,
                        'ip' => GeneralUtility::getIndpEnv('REMOTE_ADDR'),
                        'referer' => GeneralUtility::getIndpEnv('HTTP_REFERER'),
                        'request' => GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'),
                        'query_type' => GeneralUtility::trimExplode(' ', $loggedQuery['sql'], true, 2)[0],
                        'duration' => $loggedQuery['executionMS'],
                        'query' => $this->connection->quote($loggedQuery['sql']),
                        'profile' => serialize($this->getProfileInformation($loggedQuery)),
                        'explain_query' => serialize([]),
                        'not_using_index' => 0,
                        'using_fulltable' => 0,
                        'mode' => TYPO3_MODE,
                        'unique_call_identifier' => $uniqueIdentifier,
                        'crdate' => (int)$GLOBALS['EXEC_TIME'],
                        'query_id' => $key
                    ];

                    $this->addExplainInformation($queryToStore, $loggedQuery);

                    $queriesToStore[] = $queryToStore;
                }

                foreach (array_chunk($queriesToStore, 50) as $chunkOfQueriesToStore) {
                    $this->connection->bulkInsert(
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

    private function getProfileInformation(array $loggedQuery): array
    {
        if (!isset($loggedQuery['profile'])) {
            return [];
        }

        if (!is_array($loggedQuery['profile'])) {
            return [];
        }

        return $loggedQuery['profile'];
    }

    protected function addExplainInformation(array &$queryToStore, array $loggedQuery): void
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

            $statement = $this->connection->query($this->buildExplainQuery($sql, $loggedQuery['params'], $loggedQuery['types']));
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

    protected function buildExplainQuery(string $sql, array $params, array $types): string
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

    protected function getConnection(): ?Connection
    {
        try {
            return $this->getConnectionPool()->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        } catch (\UnexpectedValueException $unexpectedValueException) {
            // Should never be thrown, as a hard-coded name was added as parameter
        } catch (\RuntimeException $runtimeException) {
            // Default database of TYPO3 is not configured
        } catch (Exception $exception) {
            // Something breaks in DriverManager of Doctrine DBAL
        }

        return null;
    }

    protected function getConnectionPool(): ConnectionPool
    {
        return GeneralUtility::makeInstance(ConnectionPool::class);
    }
}

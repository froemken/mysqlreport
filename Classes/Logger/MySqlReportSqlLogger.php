<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Logger;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Logging\SQLLogger;
use StefanFroemken\Mysqlreport\Helper\SqlLoggerHelper;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This is an extended version of the DebugStack SQL logger
 * I have added profiling information
 */
class MySqlReportSqlLogger implements SQLLogger
{
    /**
     * Collected SQL queries
     *
     * @var array<int, array<string, mixed>>
     */
    public $queries = [];

    /**
     * If enabled, this class will log queries
     *
     * @var bool
     */
    public $enabled = true;

    /**
     * @var float|null
     */
    public $start;

    /**
     * @var int
     */
    public $currentQuery = 0;

    /**
     * Value from extension setting
     * Default to false, because "true" can reduce query execution a lot
     *
     * @var bool
     */
    public $addExplain = false;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var SqlLoggerHelper
     */
    protected $sqlLoggerHelper;

    public function __construct()
    {
        $this->connection = $this->getConnection();
        if (!$this->connection instanceof Connection) {
            $this->enabled = false;
        }

        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        try {
            $this->addExplain = (bool)$extensionConfiguration->get('mysqlreport', 'addExplain');
        } catch (ExtensionConfigurationExtensionNotConfiguredException | ExtensionConfigurationPathDoesNotExistException $exception) {
            $this->addExplain = false;
        }

        $this->sqlLoggerHelper = GeneralUtility::makeInstance(SqlLoggerHelper::class);
    }

    /**
     * Logs a SQL statement
     */
    public function startQuery($sql, ?array $params = null, ?array $types = null): void
    {
        if (!$this->enabled) {
            return;
        }

        $this->start = microtime(true);

        $this->queries[++$this->currentQuery] = [
            'sql' => $sql,
            'params' => $params,
            'types' => $types,
            'executionMS' => 0,
        ];

        if ($this->addExplain) {
            $currentSqlLogger = $this->sqlLoggerHelper->getCurrentSqlLogger($this->connection);
            $this->sqlLoggerHelper->deactivateSqlLogger($this->connection);
            $this->connection->executeQuery('SET profiling = 1');
            $this->sqlLoggerHelper->activateSqlLogger($this->connection, $currentSqlLogger);
        }
    }

    /**
     * Marks the last started query as stopped. This can be used for timing of queries.
     */
    public function stopQuery(): void
    {
        if (!$this->enabled) {
            return;
        }

        $this->queries[$this->currentQuery]['executionMS'] = microtime(true) - $this->start;

        if ($this->addExplain) {
            $currentSqlLogger = $this->sqlLoggerHelper->getCurrentSqlLogger($this->connection);
            $this->sqlLoggerHelper->deactivateSqlLogger($this->connection);
            $profileInformation = $this->connection->executeQuery('SHOW profile')->fetchAllAssociative();
            $this->sqlLoggerHelper->activateSqlLogger($this->connection, $currentSqlLogger);

            $this->queries[$this->currentQuery]['profile'] = $profileInformation;
        }
    }

    private function getConnection(): ?Connection
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

    private function getConnectionPool(): ConnectionPool
    {
        return GeneralUtility::makeInstance(ConnectionPool::class);
    }
}

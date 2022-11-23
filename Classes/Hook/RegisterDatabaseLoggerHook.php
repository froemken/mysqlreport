<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Hook;

use StefanFroemken\Mysqlreport\Helper\ConnectionHelper;
use StefanFroemken\Mysqlreport\Helper\SqlLoggerHelper;
use StefanFroemken\Mysqlreport\Logger\MySqlReportSqlLogger;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\TableConfigurationPostProcessingHookInterface;
use TYPO3\CMS\Core\SingletonInterface;

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
    private $extConf = [];

    /**
     * @var ConnectionHelper
     */
    private $connectionHelper;

    /**
     * @var SqlLoggerHelper
     */
    private $sqlLoggerHelper;

    public function injectExtensionConfiguration(ExtensionConfiguration $extensionConfiguration): void
    {
        try {
            $this->extConf = (array)$extensionConfiguration->get('mysqlreport');
        } catch (ExtensionConfigurationExtensionNotConfiguredException | ExtensionConfigurationPathDoesNotExistException $exception) {
            $this->extConf = [];
        }
    }

    public function injectConnectionHelper(ConnectionHelper $connectionHelper): void
    {
        $this->connectionHelper = $connectionHelper;
    }

    public function injectSqlLoggerHelper(SqlLoggerHelper $sqlLoggerHelper): void
    {
        $this->sqlLoggerHelper = $sqlLoggerHelper;
        $this->sqlLoggerHelper->setConnectionConfiguration($this->connectionHelper->getConnectionConfiguration());
    }

    public function processData(): void
    {
        if (!$this->connectionHelper->isConnectionAvailable()) {
            return;
        }

        if (!$this->isFrontendOrBackendProfilingActivated()) {
            return;
        }

        $this->sqlLoggerHelper->activateSqlLogger();
    }

    public function __destruct()
    {
        if (!$this->connectionHelper->isConnectionAvailable()) {
            return;
        }

        if (!$this->isFrontendOrBackendProfilingActivated()) {
            return;
        }

        $sqlLogger = $this->sqlLoggerHelper->getCurrentSqlLogger();
        if ($sqlLogger instanceof MySqlReportSqlLogger) {
            $queriesToStore = [];
            foreach ($sqlLogger->profiles as $key => $profile) {
                $queryToStore = [
                    'pid' => $profile->getPid(),
                    'ip' => $profile->getIp(),
                    'referer' => $profile->getReferer(),
                    'request' => $profile->getRequest(),
                    'query_type' => $profile->getQueryType(),
                    'duration' => $profile->getDuration(),
                    'query' => $profile->getQueryWithReplacedParameters(),
                    'profile' => serialize($profile->getProfile()),
                    'explain_query' => serialize($profile->getExplainInformation()->getExplainResults()),
                    'not_using_index' => $profile->getExplainInformation()->isQueryUsingIndex() ? 0 : 1,
                    'using_fulltable' => $profile->getExplainInformation()->isQueryUsingFTS() ? 1 : 0,
                    'mode' => $profile->getMode(),
                    'unique_call_identifier' => $profile->getUniqueCallIdentifier(),
                    'crdate' => $profile->getCrdate(),
                    'query_id' => $key
                ];

                $queriesToStore[] = $queryToStore;
            }

            foreach (array_chunk($queriesToStore, 50) as $chunkOfQueriesToStore) {
                $this->connectionHelper->bulkInsert(
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

    private function isFrontendOrBackendProfilingActivated(): bool
    {
        if (
            isset($this->extConf['profileFrontend'])
            && $this->extConf['profileFrontend']
            && TYPO3_MODE === 'FE'
        ) {
            return true;
        }

        if (
            isset($this->extConf['profileBackend'])
            && $this->extConf['profileBackend']
            && TYPO3_MODE === 'BE'
        ) {
            return true;
        }

        return false;
    }
}

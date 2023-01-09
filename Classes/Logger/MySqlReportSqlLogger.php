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
use StefanFroemken\Mysqlreport\Domain\Factory\ProfileFactory;
use StefanFroemken\Mysqlreport\Domain\Model\Profile;
use StefanFroemken\Mysqlreport\Helper\ConnectionHelper;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * This is an extended version of the DebugStack SQL logger
 * I have added profiling information
 */
class MySqlReportSqlLogger implements SQLLogger
{
    /**
     * Collected profiles
     *
     * @var \SplQueue|Profile[]
     */
    public \SplQueue $profiles;

    protected ConnectionHelper $connectionHelper;

    protected ProfileFactory $profileFactory;

    /**
     * If enabled, this class will log queries
     */
    public bool $enabled = true;

    /**
     * Value from extension setting
     * Default to false, because "true" can reduce query execution a lot
     */
    public bool $addExplain = false;

    public float $start = 0.0;

    public int $queryIterator = 0;

    public function injectExtensionConfiguration(ExtensionConfiguration $extensionConfiguration): void
    {
        try {
            $this->addExplain = (bool)$extensionConfiguration->get('mysqlreport', 'addExplain');
        } catch (ExtensionConfigurationExtensionNotConfiguredException | ExtensionConfigurationPathDoesNotExistException $exception) {
            $this->addExplain = false;
        }
    }

    public function injectConnectionHelper(ConnectionHelper $connectionHelper): void
    {
        $this->connectionHelper = $connectionHelper;
        if (!$this->connectionHelper->isConnectionAvailable()) {
            $this->enabled = false;
        }
    }

    public function injectProfileFactory(ProfileFactory $profileFactory): void
    {
        $this->profileFactory = $profileFactory;
    }

    public function __construct()
    {
        $this->profiles = new \SplQueue();
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

        $profile = $this->profileFactory->createNewProfile();
        $profile->setQuery($sql);
        $profile->setQueryParameters($params);
        $profile->setQueryParameterTypes($types);

        $this->profiles->add($this->queryIterator, $profile);

        if ($this->addExplain) {
            $this->connectionHelper->executeQuery('SET profiling = 1');
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

        $this->profiles[$this->queryIterator]->setDuration(microtime(true) - $this->start);

        if ($this->addExplain) {
            if ($result = $this->connectionHelper->executeQuery('SHOW profile')) {
                $this->profiles[$this->queryIterator]->setProfile($result->fetchAll());
            }
            $this->updateExplainInformation($this->profiles[$this->queryIterator]);
        }

        $this->queryIterator++;
    }

    private function updateExplainInformation(Profile $profile): void
    {
        if ($this->addExplain === false) {
            return;
        }

        if ($profile->getQueryType() !== 'SELECT') {
            return;
        }

        try {
            if ($result = $this->connectionHelper->executeQuery($profile->getQueryForExplain())) {
                while ($explainResult = $result->fetchAssociative()) {
                    if (empty($explainResult['key'])) {
                        $profile->getExplainInformation()->setIsQueryUsingIndex(false);
                    }

                    if (strtolower($explainResult['select_type'] ?? '') === 'all') {
                        $profile->getExplainInformation()->setIsQueryUsingFTS(true);
                    }

                    $profile->getExplainInformation()->addExplainResult($explainResult);
                }
            }
        } catch (Exception|\Doctrine\DBAL\Driver\Exception $exception) {
            // Leave ExplainInformation unmodified
        }
    }
}

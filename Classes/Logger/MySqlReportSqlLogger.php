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
use StefanFroemken\Mysqlreport\Configuration\ExtConf;
use StefanFroemken\Mysqlreport\Domain\Factory\ProfileFactory;
use StefanFroemken\Mysqlreport\Domain\Model\Profile;
use StefanFroemken\Mysqlreport\Traits\Typo3RequestTrait;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This logger is wrapped around the query and command execution of doctrine to collect duration and
 * other query information.
 */
class MySqlReportSqlLogger
{
    use Typo3RequestTrait;

    /**
     * Collected profiles
     *
     * @var \SplQueue|Profile[]
     */
    private \SplQueue $profiles;

    /**
     * If activated, we will execute each query additional with prepended EXPLAIN
     * to get more information about indexing and FTS.
     * Default is false, to prevent memory usage and performance.
     * This value can be set in extension settings
     */
    private bool $addAdditionalQueryExplain = false;

    /**
     * This value will be set for each query to current micro-time.
     * Needed to calculate the query duration.
     * Must be a class property to transfer micro-time from startQuery to stopQuery.
     *
     * @var float
     */
    private float $queryStartTime = 0.0;

    /**
     * It looks like a counter, but will be used to order the queries into correct execution position
     * while listing them in BE module.
     */
    private int $queryIterator = 0;

    /**
     * Every query which contains one of these parts will be skipped.
     */
    private array $skipQueries = [
        'show global status',
        'show global variables',
        'tx_mysqlreport_domain_model_profile',
    ];

    public function __construct(
        private readonly ProfileFactory $profileFactory,
        private readonly ExtConf $extConf,
    ) {
        $this->profiles = new \SplQueue();
    }

    /**
     * This method will be called just before the query will be executed by doctrine.
     * Prepare query profiling.
     */
    public function startQuery(): void
    {
        $this->queryStartTime = microtime(true);
    }

    /**
     * This method will be called just after the query has been executed by doctrine.
     * Start collecting duration and other stuff.
     */
    public function stopQuery($query): void
    {
        if (!$this->isValidQuery($query)) {
            return;
        }

        $profile = $this->profileFactory->createNewProfile();
        $profile->setQuery($query);
        $profile->setDuration(microtime(true) - $this->queryStartTime);
        // $profile->setQueryParameters($params);
        // $profile->setQueryParameterTypes($types);

        $this->profiles->add($this->queryIterator, $profile);

        $this->queryIterator++;
    }

    private function isValidQuery(string $query): bool
    {
        foreach ($this->skipQueries as $skipQuery) {
            if (str_contains(strtolower($query), $skipQuery)) {
                return false;
            }
        }

        return true;
    }

    public function __destruct()
    {
        if (!$this->isFrontendOrBackendProfilingActivated()) {
            return;
        }

        $queriesToStore = [];
        foreach ($this->profiles as $key => $profile) {
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
                    'profile',
                    'explain_query',
                    'not_using_index',
                    'using_fulltable',
                    'mode',
                    'unique_call_identifier',
                    'crdate',
                    'query_id',
                ]
            );
        }
    }

    private function isFrontendOrBackendProfilingActivated(): bool
    {
        if (Environment::isCli()) {
            return false;
        }

        if ($this->extConf->isProfileBackend() && !$this->isBackendRequest()) {
            return true;
        }

        if ($this->extConf->isProfileBackend() && $this->isBackendRequest()) {
            return true;
        }

        return false;
    }

    /**
     * Bulk insert. This method will also be caught by our logger, but as it was called
     * at last position of __destruct, it will not be stored in profile table.
     */
    private function bulkInsert(array $data, array $columns = []): void
    {
        try {
            GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME)
                ->bulkInsert('tx_mysqlreport_domain_model_profile', $data, $columns);
        } catch (Exception $e) {
        }
    }
}

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
use Doctrine\DBAL\ParameterType;
use StefanFroemken\Mysqlreport\Configuration\ExtConf;
use StefanFroemken\Mysqlreport\Domain\Factory\ProfileFactory;
use StefanFroemken\Mysqlreport\Domain\Model\Profile;
use StefanFroemken\Mysqlreport\Helper\ExplainQueryHelper;
use StefanFroemken\Mysqlreport\Helper\QueryParamsHelper;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This logger is wrapped around the query and command execution of doctrine to collect duration and
 * other query information.
 */
class MySqlReportSqlLogger
{
    /**
     * Collected profiles
     *
     * @var \SplQueue<Profile>|Profile[]
     */
    private \SplQueue $profiles;

    /**
     * It looks like a counter, but will be used to order the queries into correct execution position
     * while listing them in BE module.
     */
    private int $queryIterator = 0;

    /**
     * Every query which contains one of these parts will be skipped.
     *
     * @var string[]
     */
    private array $skipQueries = [
        'SELECT DATABASE()',
        'show global status',
        'show global variables',
        'tx_mysqlreport_domain_model_profile',
        'information_schema',
    ];

    public function __construct(
        private readonly ProfileFactory $profileFactory,
        private readonly ExtConf $extConf,
        private readonly QueryParamsHelper $queryParamsHelper,
        private readonly ExplainQueryHelper $explainQueryHelper,
    ) {
        $this->profiles = new \SplQueue();
    }

    /**
     * This method will be called just after the query has been executed by doctrine.
     * Start collecting duration and other stuff.
     *
     * @param array<int, string> $params
     * @param array<int, ParameterType> $types
     */
    public function stopQuery(string $query, float $duration, array $params = [], array $types = []): void
    {
        if (!$this->isValidQuery($query)) {
            return;
        }

        $profile = $this->profileFactory->createNewProfile();
        $profile->setDuration($duration);
        $profile->setQuery($this->queryParamsHelper->getQueryWithReplacedParams($query, $params, $types));

        $this->profiles->add($this->queryIterator, $profile);

        $this->queryIterator++;
    }

    private function isValidQuery(string $query): bool
    {
        if (str_starts_with($query, 'EXPLAIN')) {
            return false;
        }

        foreach ($this->skipQueries as $skipQuery) {
            if (str_contains(strtolower($query), $skipQuery)) {
                return false;
            }
        }

        return true;
    }



    private function getTypo3DefaultConnection(): ?Connection
    {
        try {
            return GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        } catch (Exception $e) {
        }

        return null;
    }

    /**
     * Bulk insert. This method will also be caught by our logger, but as it was called
     * at last position of __destruct, it will not be stored in profile table.
     *
     * @param array<int, array<mixed>> $data
     * @param array<int, string> $columns
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

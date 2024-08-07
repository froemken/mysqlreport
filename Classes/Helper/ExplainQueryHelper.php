<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Helper;

use Doctrine\DBAL\Exception;
use StefanFroemken\Mysqlreport\Configuration\ExtConf;
use StefanFroemken\Mysqlreport\Domain\Model\QueryInformation;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Helper to analyze EXPLAIN query result and add information to QueryInformation model
 */
readonly class ExplainQueryHelper
{
    /**
     * @param QueryInformation $queryInformation
     */
    public function updateQueryInformation(QueryInformation $queryInformation): void
    {
        if (!$this->getExtConf()->isActivateExplainQuery()) {
            return;
        }

        if ($queryInformation->getQueryType() !== 'SELECT') {
            return;
        }

        $isQueryUsingIndex = null;
        $isQueryUsingFts = null;
        foreach ($this->getExplainRows($queryInformation) as $explainRow) {
            $queryInformation->getExplainInformation()->addExplainResult($explainRow);

            if ($isQueryUsingIndex === null && empty($explainRow['key'])) {
                $queryInformation->getExplainInformation()->setIsQueryUsingIndex(false);
                $isQueryUsingIndex = false;
            }

            if ($isQueryUsingFts === null && strtolower($explainRow['type'] ?? '') === 'all') {
                $queryInformation->getExplainInformation()->setIsQueryUsingFTS(true);
                $isQueryUsingFts = true;
            }
        }
    }

    private function getExplainRows(QueryInformation $queryInformation): array
    {
        $explainRows = [];
        try {
            $queryResult = $this->getDefaultConnection()->executeQuery(
                'EXPLAIN ' . $queryInformation->getQuery(),
            );

            while ($explainRow = $queryResult->fetchAssociative()) {
                $explainRows[] = $explainRow;
            }
        } catch (Exception $e) {
        }

        return $explainRows;
    }

    private function getExtConf(): ExtConf
    {
        return GeneralUtility::makeInstance(ExtConf::class);
    }

    private function getDefaultConnection(): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
    }
}

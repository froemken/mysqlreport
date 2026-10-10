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
use Psr\Log\LoggerInterface;
use StefanFroemken\Mysqlreport\Configuration\ExtConf;
use StefanFroemken\Mysqlreport\Domain\Model\QueryInformation;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Helper to analyze an EXPLAIN query result and add information to QueryInformation model
 */
final readonly class ExplainQueryHelper
{
    public function __construct(
        private ConnectionPool $connectionPool,
        private ExtConf $extConf,
        private LoggerInterface $logger,
    ) {}

    public function updateQueryInformation(QueryInformation $queryInformation): void
    {
        if (!$this->extConf->isActivateExplainQuery()) {
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

    /**
     * @return array<int, array<string, string>>
     */
    private function getExplainRows(QueryInformation $queryInformation): array
    {
        $explainRows = [];
        try {
            $connection = $this->connectionPool->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
            $queryResult = $connection->executeQuery('EXPLAIN ' . $queryInformation->getQuery());

            while ($explainRow = $queryResult->fetchAssociative()) {
                $explainRows[] = $explainRow;
            }
        } catch (Exception $exception) {
            $this->logger->error('Error while executing EXPLAIN query', [
                'exception' => $exception,
            ]);
        }

        return $explainRows;
    }
}

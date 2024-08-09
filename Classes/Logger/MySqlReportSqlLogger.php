<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Logger;

use Doctrine\DBAL\ParameterType;
use StefanFroemken\Mysqlreport\Domain\Factory\QueryInformationFactory;
use StefanFroemken\Mysqlreport\Domain\Model\QueryInformation;
use StefanFroemken\Mysqlreport\Helper\QueryParamsHelper;

/**
 * This logger is wrapped around the query and command execution of doctrine to collect duration and
 * other query information.
 */
readonly class MySqlReportSqlLogger implements LoggerInterface
{
    /**
     * Every query which contains one of these parts will be skipped.
     *
     * @var string[]
     */
    private const SKIP_QUERIES = [
        'SELECT DATABASE()',
        'show global status',
        'show global variables',
        'tx_mysqlreport_query_information',
        'information_schema',
    ];

    public function __construct(
        private QueryInformationFactory $queryInformationFactory,
        private QueryParamsHelper $queryParamsHelper,
    ) {}

    /**
     * This method will be called just after the query has been executed by doctrine.
     * Start collecting duration and other stuff.
     *
     * @param array<int, string> $params
     * @param array<int, string> $types
     */
    public function stopQuery(string $query, float $duration, array $params = [], array $types = []): ?QueryInformation
    {
        if (!$this->isValidQuery($query)) {
            return null;
        }

        $queryInformation = $this->queryInformationFactory->createNewQueryInformation();
        $queryInformation->setDuration($duration);
        $queryInformation->setQuery($this->queryParamsHelper->getQueryWithReplacedParams($query, $params, $types));

        return $queryInformation;
    }

    private function isValidQuery(string $query): bool
    {
        if (str_starts_with($query, 'EXPLAIN')) {
            return false;
        }

        foreach (self::SKIP_QUERIES as $skipQuery) {
            if (str_contains(strtolower($query), $skipQuery)) {
                return false;
            }
        }

        return true;
    }
}

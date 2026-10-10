<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Helper;

use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Helper to replace query params in a query to get a ready to use query for EXPLAIN
 */
final readonly class QueryParamsHelper
{
    public function __construct(
        private ConnectionPool $connectionPool,
        private LoggerInterface $logger,
    ) {}

    /**
     * @param array<int|string, mixed> $params
     * @param array<int|string, int|string> $types
     */
    public function getQueryWithReplacedParams(
        string $sql,
        array $params = [],
        array $types = [],
    ): string {
        if ($params === []) {
            return $sql;
        }

        try {
            return $this->parseAndReplaceParams($sql, $params, $types);
        } catch (\Throwable $exception) {
            $this->logger->error('Error while replacing placeholders in query', [
                'exception' => $exception,
            ]);
            return $sql;
        }
    }

    /**
     * @param array<int|string, mixed> $params
     * @param array<int|string, int|string> $types
     */
    private function parseAndReplaceParams(string $sql, array $params, array $types): string
    {
        $platform = $this->connectionPool->getConnectionByName(
            ConnectionPool::DEFAULT_CONNECTION_NAME,
        )->getDatabasePlatform();

        $visitor = new SqlParamSubstitutionVisitor($params, $types, $platform);
        $platform->createSQLParser()->parse($sql, $visitor);

        return $visitor->getSql();
    }
}

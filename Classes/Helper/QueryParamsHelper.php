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
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Helper to replace query params in query to get a ready to use query for EXPLAIN
 */
readonly class QueryParamsHelper
{
    public function __construct(private ConnectionPool $connectionPool) {}

    /**
     * @param ParameterType[] $types
     * @return string
     */
    public function getQueryWithReplacedParams(
        string $sql,
        array $params = [],
        array $types = [],
    ): string
    {
        try {
            if ($params !== []) {
                $dbPlatform = $this->connectionPool->getConnectionByName(
                    ConnectionPool::DEFAULT_CONNECTION_NAME
                )->getDatabasePlatform();

                foreach ($params as $key => $param) {
                    $type = Type::getType(strtolower($types[$key]->name));
                    $value = $type->convertToDatabaseValue($param, $dbPlatform);
                    $sql = join(
                        var_export($value, true),
                        explode('?', $sql, 2)
                    );
                }
            }
        } catch (ConversionException|Exception $e) {
        }

        return $sql;
    }
}

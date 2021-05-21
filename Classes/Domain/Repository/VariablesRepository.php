<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Repository to get the MySQL variables
 */
class VariablesRepository extends AbstractRepository
{
    public function findAll(): array
    {
        $connection = $this
            ->getConnectionPool()
            ->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        $statement = $connection->executeQuery('SHOW GLOBAL VARIABLES');

        $rows = [];
        while ($row = $statement->fetchAssociative()) {
            $rows[$row['Variable_name']] = $row['Value'];
        }

        return $rows;
    }
}

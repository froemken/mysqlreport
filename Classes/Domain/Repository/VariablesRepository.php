<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Repository;

use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Repository to get the MySQL variables
 */
class VariablesRepository extends AbstractRepository
{
    public function findAll(): Variables
    {
        $connection = $this->getConnectionPool()->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        $statement = $connection->query('SHOW GLOBAL VARIABLES');

        $rows = [];
        while ($row = $statement->fetch()) {
            $rows[strtolower($row['Variable_name'])] = $row['Value'];
        }

        /** @var Variables $variables */
        $variables = $this->dataMapper->mapSingleRow(Variables::class, $rows);

        return $variables;
    }
}

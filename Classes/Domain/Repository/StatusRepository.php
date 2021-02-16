<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Repository;

use StefanFroemken\Mysqlreport\Domain\Model\Status;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Repository to get the MySQL status
 */
class StatusRepository extends AbstractRepository
{
    public function findAll(): Status
    {
        $connection = $this->getConnectionPool()->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        $statement = $connection->query('SHOW GLOBAL STATUS');

        $rows = [];
        while ($row = $statement->fetch()) {
            $rows[strtolower($row['Variable_name'])] = $row['Value'];
        }

        /** @var Status $status */
        $status = $this->dataMapper->mapSingleRow(Status::class, $rows);

        return $status;
    }
}

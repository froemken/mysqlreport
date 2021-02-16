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

/**
 * Repository to get the MySQL status
 */
class StatusRepository extends AbstractRepository
{
    public function findAll(): Status
    {
        $rows = [];
        $res = $this->databaseConnection->sql_query('SHOW GLOBAL STATUS;');
        while ($row = $this->databaseConnection->sql_fetch_assoc($res)) {
            $rows[strtolower($row['Variable_name'])] = $row['Value'];
        }

        /** @var Status $status */
        $status = $this->dataMapper->mapSingleRow(Status::class, $rows);

        return $status;
    }
}

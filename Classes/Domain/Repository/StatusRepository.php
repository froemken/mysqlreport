<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Repository;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;

/**
 * Repository to get the MySQL/MariaDB STATUS values
 */
class StatusRepository extends AbstractRepository
{
    public function findAll(): StatusValues
    {
        $statement = $this->connectionHelper->executeQuery('SHOW GLOBAL STATUS');
        if ($statement === null) {
            return new StatusValues([]);
        }

        $rows = [];
        while ($row = $statement->fetch()) {
            $rows[$row['Variable_name']] = $row['Value'];
        }

        return new StatusValues($rows);
    }
}

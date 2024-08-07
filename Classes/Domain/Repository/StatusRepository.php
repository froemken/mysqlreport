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
use StefanFroemken\Mysqlreport\Helper\ConnectionHelper;

/**
 * Repository to get the MySQL/MariaDB STATUS values
 */
class StatusRepository
{
    public function __construct(readonly private ConnectionHelper $connectionHelper)
    {}

    public function findAll(): StatusValues
    {
        $result = $this->connectionHelper->executeQuery('SHOW GLOBAL STATUS');
        if ($result === null) {
            return new StatusValues([]);
        }

        $rows = [];
        while ($row = $result->fetchAssociative()) {
            $rows[$row['Variable_name']] = $row['Value'];
        }

        return new StatusValues($rows);
    }
}

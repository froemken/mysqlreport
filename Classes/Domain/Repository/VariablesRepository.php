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
use StefanFroemken\Mysqlreport\Helper\ConnectionHelper;

/**
 * Repository to get the MySQL/MariaDB VARIABLES
 */
class VariablesRepository
{
    public function __construct(readonly private ConnectionHelper $connectionHelper)
    {}

    public function findAll(): Variables
    {
        $result = $this->connectionHelper->executeQuery('SHOW GLOBAL VARIABLES');
        if ($result === null) {
            return new Variables([]);
        }

        $rows = [];
        while ($row = $result->fetchAssociative()) {
            $rows[$row['Variable_name']] = $row['Value'];
        }

        return new Variables($rows);
    }
}

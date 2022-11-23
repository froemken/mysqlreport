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

/**
 * Repository to get the MySQL/MariaDB VARIABLES
 */
class VariablesRepository extends AbstractRepository
{
    public function findAll(): Variables
    {
        $result = $this->connectionHelper->executeQuery('SHOW GLOBAL VARIABLES');
        if ($result === null) {
            return new Variables([]);
        }

        $rows = [];
        while ($row = $result->fetch()) {
            $rows[$row['Variable_name']] = $row['Value'];
        }

        return new Variables($rows);
    }
}

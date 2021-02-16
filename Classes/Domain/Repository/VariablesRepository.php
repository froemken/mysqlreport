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
 * Repository to get the MySQL variables
 */
class VariablesRepository extends AbstractRepository
{
    public function findAll(): Variables
    {
        $rows = [];
        $res = $this->databaseConnection->sql_query('SHOW GLOBAL VARIABLES;');

        while ($row = $this->databaseConnection->sql_fetch_assoc($res)) {
            $rows[strtolower($row['Variable_name'])] = $row['Value'];
        }

        /** @var Variables $variables */
        $variables = $this->dataMapper->mapSingleRow(Variables::class, $rows);

        return $variables;
    }
}

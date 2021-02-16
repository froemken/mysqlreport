<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Repository;

/**
 * Repository to get the MySQL variables
 */
class VariablesRepository extends AbstractRepository
{
    /**
     * get status from MySql
     *
     * @return \StefanFroemken\Mysqlreport\Domain\Model\Variables
     */
    public function findAll()
    {
        $rows = [];
        $res = $this->databaseConnection->sql_query('SHOW GLOBAL VARIABLES;');
        while ($row = $this->databaseConnection->sql_fetch_assoc($res)) {
            $rows[strtolower($row['Variable_name'])] = $row['Value'];
        }
        return $this->dataMapper->mapSingleRow('StefanFroemken\\Mysqlreport\\Domain\\Model\\Variables', $rows);
    }
}

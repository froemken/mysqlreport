<?php
namespace StefanFroemken\Mysqlreport\Domain\Repository;
    
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
    
/**
 * This model saves the mysql status
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
        $rows = array();
        $res = $this->databaseConnection->sql_query('SHOW GLOBAL VARIABLES;');
        while ($row = $this->databaseConnection->sql_fetch_assoc($res)) {
            $rows[strtolower($row['Variable_name'])] = $row['Value'];
        }
        return $this->dataMapper->mapSingleRow('StefanFroemken\\Mysqlreport\\Domain\\Model\\Variables', $rows);
    }

}

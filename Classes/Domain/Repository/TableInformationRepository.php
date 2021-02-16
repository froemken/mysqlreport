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
 * Repository to get table information
 */
class TableInformationRepository extends AbstractRepository
{
    const INNODB = 'InnoDB';
    const MYISAM = 'MyISAM';

    /**
     * get table informations from information_scheme
     *
     * @return array
     */
    public function findAll()
    {
        $rows = [];
        $res = $this->databaseConnection->sql_query('
            SELECT *
            FROM information_schema.TABLES
            WHERE table_schema = "' . TYPO3_db . '";
        ');
        while ($row = $this->databaseConnection->sql_fetch_assoc($res)) {
            $rows[$row['TABLE_NAME']] = $this->dataMapper->mapSingleRow('StefanFroemken\\Mysqlreport\\Domain\\Model\\TableInformation', $row);
        }
        return $rows;
    }

    /**
     * get table informations of a given engine from information_scheme
     *
     * @param string $engine
     * @return array
     */
    public function findAllByEngine($engine)
    {
        $rows = [];
        $res = $this->databaseConnection->sql_query('
            SELECT *
            FROM information_schema.TABLES
            WHERE table_schema = "' . TYPO3_db . '"
            AND ENGINE = "' . $engine . '";
        ');
        while ($row = $this->databaseConnection->sql_fetch_assoc($res)) {
            $rows[$row['TABLE_NAME']] = $this->dataMapper->mapSingleRow('StefanFroemken\\Mysqlreport\\Domain\\Model\\TableInformation', $row);
        }
        return $rows;
    }

    /**
     * get table information of a given table from information_scheme
     *
     * @param string $table
     * @return \StefanFroemken\Mysqlreport\Domain\Model\TableInformation[]
     */
    public function findByTable($table)
    {
        $res = $this->databaseConnection->sql_query('
            SELECT *
            FROM information_schema.TABLES
            WHERE table_schema = "' . TYPO3_db . '"
            AND TABLE_NAME = "' . $table . '";
        ');
        $rows = [];
        while ($row = $this->databaseConnection->sql_fetch_assoc($res)) {
            $rows[$row['TABLE_NAME']] = $this->dataMapper->mapSingleRow('StefanFroemken\\Mysqlreport\\Domain\\Model\\TableInformation', $row);
        }
        return $rows;
    }

    /**
     * get indexSize from information_scheme
     *
     * @param string $engine
     * @return array
     */
    public function getIndexSize($engine = '')
    {
        $additionalWhere = '';
        if (!empty($engine)) {
            $additionalWhere = ' AND ENGINE = "' . $engine . '"';
        }
        $res = $this->databaseConnection->sql_query('
            SELECT SUM(INDEX_LENGTH) AS indexsize
            FROM information_schema.TABLES
            WHERE table_schema = "' . TYPO3_db . '"' .
            $additionalWhere . ';
        ');
        $row = $this->databaseConnection->sql_fetch_assoc($res);
        
        return $row['indexsize'];
    }
}

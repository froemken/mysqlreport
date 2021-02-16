<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Repository;

use StefanFroemken\Mysqlreport\Domain\Model\TableInformation;

/**
 * Repository to get table information
 */
class TableInformationRepository extends AbstractRepository
{
    const INNODB = 'InnoDB';
    const MYISAM = 'MyISAM';

    public function findAll(): array
    {
        $rows = [];
        $res = $this->databaseConnection->sql_query('
            SELECT *
            FROM information_schema.TABLES
            WHERE table_schema = "' . TYPO3_db . '";
        ');
        while ($row = $this->databaseConnection->sql_fetch_assoc($res)) {
            $rows[$row['TABLE_NAME']] = $this->dataMapper->mapSingleRow(TableInformation::class, $row);
        }

        return $rows;
    }

    public function findAllByEngine(string $engine): array
    {
        $rows = [];
        $res = $this->databaseConnection->sql_query('
            SELECT *
            FROM information_schema.TABLES
            WHERE table_schema = "' . TYPO3_db . '"
            AND ENGINE = "' . $engine . '";
        ');
        while ($row = $this->databaseConnection->sql_fetch_assoc($res)) {
            $rows[$row['TABLE_NAME']] = $this->dataMapper->mapSingleRow(TableInformation::class, $row);
        }
        return $rows;
    }

    /**
     * @param string $table
     * @return array|TableInformation[]
     */
    public function findByTable(string $table): array
    {
        $res = $this->databaseConnection->sql_query('
            SELECT *
            FROM information_schema.TABLES
            WHERE table_schema = "' . TYPO3_db . '"
            AND TABLE_NAME = "' . $table . '";
        ');

        $rows = [];
        while ($row = $this->databaseConnection->sql_fetch_assoc($res)) {
            $rows[$row['TABLE_NAME']] = $this->dataMapper->mapSingleRow(TableInformation::class, $row);
        }

        return $rows;
    }

    public function getIndexSize(string $engine = ''): array
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

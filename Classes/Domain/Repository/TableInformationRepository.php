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
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Repository to get table information
 */
class TableInformationRepository extends AbstractRepository
{
    public function findAll(): array
    {
        $connection = $this->getConnectionPool()->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        $statement = $connection->query('
            SELECT *
            FROM information_schema.TABLES
            WHERE table_schema = "' . TYPO3_db . '";
        ');

        $rows = [];
        while ($row = $statement->fetch()) {
            $rows[$row['TABLE_NAME']] = $this->dataMapper->mapSingleRow(TableInformation::class, $row);
        }

        return $rows;
    }

    public function findAllByEngine(string $engine): array
    {
        $connection = $this->getConnectionPool()->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        $statement = $connection->query('
            SELECT *
            FROM information_schema.TABLES
            WHERE table_schema = "' . TYPO3_db . '"
            AND ENGINE = "' . $engine . '";
        ');

        $rows = [];
        while ($row = $statement->fetch()) {
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
        $connection = $this->getConnectionPool()->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        $statement = $connection->query('
            SELECT *
            FROM information_schema.TABLES
            WHERE table_schema = "' . TYPO3_db . '"
            AND TABLE_NAME = "' . $table . '";
        ');

        $rows = [];
        while ($row = $statement->fetch()) {
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

        $connection = $this->getConnectionPool()->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        $statement = $connection->query('
            SELECT SUM(INDEX_LENGTH) AS indexsize
            FROM information_schema.TABLES
            WHERE table_schema = "' . TYPO3_db . '"' .
            $additionalWhere . ';
        ');

        $row = $statement->fetch();

        return $row['indexsize'];
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Repository;

use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Repository to get the MySQL/MariaDB VARIABLES
 */
final readonly class VariablesRepository
{
    public function __construct(
        private ConnectionPool $connectionPool,
        private LoggerInterface $logger,
    ) {}

    public function findAll(): Variables
    {
        $rows = [];

        try {
            $connection = $this->connectionPool->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
            $result = $connection->executeQuery('SHOW GLOBAL VARIABLES');
            while ($row = $result->fetchAssociative()) {
                $rows[$row['Variable_name']] = $row['Value'];
            }
        } catch (Exception $exception) {
            $this->logger->error('Error while retrieving variables from database server', [
                'exception' => $exception,
            ]);
        }

        return new Variables($rows);
    }
}

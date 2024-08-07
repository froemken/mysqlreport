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
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\Traits\DatabaseConnectionTrait;

/**
 * Repository to get the MySQL/MariaDB VARIABLES
 */
class VariablesRepository
{
    use DatabaseConnectionTrait;

    public function findAll(): Variables
    {
        $rows = [];

        try {
            $result = $this->getDefaultConnection()->executeQuery('SHOW GLOBAL VARIABLES');
            while ($row = $result->fetchAssociative()) {
                $rows[$row['Variable_name']] = $row['Value'];
            }
        } catch (Exception $e) {
        }

        return new Variables($rows);
    }
}

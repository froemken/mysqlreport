<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Repository;

use StefanFroemken\Mysqlreport\Utility\DataMapper;
use TYPO3\CMS\Core\Database\DatabaseConnection;

/**
 * Abstract Repository
 */
abstract class AbstractRepository
{
    /**
     * @var DataMapper
     */
    protected $dataMapper;

    /**
     * @var DatabaseConnection
     */
    protected $databaseConnection;

    public function injectDataMapper(DataMapper $dataMapper)
    {
        $this->dataMapper = $dataMapper;
    }

    public function initializeObject()
    {
        $this->databaseConnection = $GLOBALS['TYPO3_DB'];
    }
}

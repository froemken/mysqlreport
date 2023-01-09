<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Repository;

use StefanFroemken\Mysqlreport\Configuration\ExtConf;
use StefanFroemken\Mysqlreport\Helper\ConnectionHelper;

/**
 * Abstract Repository
 */
abstract class AbstractRepository
{
    protected ConnectionHelper $connectionHelper;

    protected ExtConf $extConf;

    public function __construct(ConnectionHelper $connectionHelper, ExtConf $extConf)
    {
        $this->connectionHelper = $connectionHelper;
        $this->extConf = $extConf;
    }
}

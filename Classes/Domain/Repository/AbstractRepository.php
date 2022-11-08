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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Abstract Repository
 */
abstract class AbstractRepository
{
    /**
     * @var ConnectionHelper
     */
    protected $connectionHelper;

    /**
     * @var ExtConf
     */
    protected $extConf;

    public function __construct(ConnectionHelper $connectionHelper = null, ExtConf $extConf = null)
    {
        $this->connectionHelper = $connectionHelper ?? GeneralUtility::makeInstance(ConnectionHelper::class);
        $this->extConf = $extConf ?? GeneralUtility::makeInstance(ExtConf::class);
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\QueryCache;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\Helper\QueryCacheHelper;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\Menu\Page;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * InfoBox to inform about current query cache insert ratio
 */
class InsertRatioInfoBox extends AbstractInfoBox
{
    protected $pageIdentifier = 'queryCache';

    protected $title = 'Insert Ratio';

    /**
     * @var QueryCacheHelper
     */
    private $queryCacheHelper;

    public function injectQueryCacheHelper(QueryCacheHelper $queryCacheHelper): void
    {
        $this->queryCacheHelper = $queryCacheHelper;
    }

    public function renderBody(Page $page): string
    {
        if (
            !isset($page->getStatusValues()['Qcache_hits'])
            || (int)$page->getStatusValues()['Qcache_hits'] === 0
            || !$this->queryCacheHelper->isQueryCacheEnabled($page)
        ) {
            $this->shouldBeRendered = false;
            return '';
        }

        $content = [];
        $content[] = 'This indicates if queries are written into cache very often.';
        $content[] = 'If you have just started your MySQL-Server or have less SELECT-Statement this value can be red or yellow.';
        $content[] = 'So please wait till all caches are warmed up.';
        $content[] = "\n\n";
        $content[] = 'Insert Ratio: %f%%';

        return sprintf(
            implode(' ', $content),
            $this->getInsertRatio($page->getStatusValues())
        );
    }

    protected function getInsertRatio(StatusValues $status): float
    {
        $insertRatio = ($status['Qcache_inserts'] / ($status['Qcache_hits'] + $status['Com_select'])) * 100;
        if ($insertRatio <= 20) {
            $this->setState(StateEnumeration::STATE_OK);
        } elseif ($insertRatio <= 40) {
            $this->setState(StateEnumeration::STATE_WARNING);
        } else {
            $this->setState(StateEnumeration::STATE_ERROR);
        }

        return round($insertRatio, 4);
    }
}

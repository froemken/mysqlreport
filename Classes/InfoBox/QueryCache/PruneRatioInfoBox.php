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
 * InfoBox to inform about current query cache prune ratio
 */
class PruneRatioInfoBox extends AbstractInfoBox
{
    protected $pageIdentifier = 'queryCache';

    protected $title = 'Prune Ratio';

    public function renderBody(Page $page): string
    {
        if (
            !isset($page->getStatusValues()['Qcache_inserts'])
            || (int)$page->getStatusValues()['Qcache_inserts'] === 0
            || !$this->getQueryCacheHelper()->isQueryCacheEnabled($page)
        ) {
            $this->shouldBeRendered = false;
            return '';
        }

        $content = [];
        $content[] = 'This is the ratio between deleted and inserted queries in cache.';
        $content[] = 'If a new query was written into Cache and prune increases, it may indicate,';
        $content[] = 'that query_cache_size is too small.';
        $content[] = 'But be careful and don\'t set query_cache_size too high.';
        $content[] = 'A maximum of 256MB should be OK.';
        $content[] = 'If ratio is still high you should check your query_cache_limit.';
        $content[] = 'Please check section "Average used blocks" below';
        $content[] = 'If ratio is still too high you have too much different queries.';
        $content[] = 'Please deactivate Query Cache completely.';
        $content[] = 'It does not make sense to increase query_cache_size,';
        $content[] = 'because of mutex/concurrency while updating or deleting records';
        $content[] = "\n\n";
        $content[] = 'Prune Ratio: %f%%';

        return sprintf(
            implode(' ', $content),
            $this->getPruneRatio($page->getStatusValues())
        );
    }

    protected function getPruneRatio(StatusValues $status): float
    {
        $pruneRatio = 0;
        if ($status['Qcache_inserts']) {
            $pruneRatio = ($status['Qcache_lowmem_prunes'] / $status['Qcache_inserts']) * 100;
        }

        if ($pruneRatio <= 10) {
            $this->setState(StateEnumeration::STATE_OK);
        } elseif ($pruneRatio <= 40) {
            $this->setState(StateEnumeration::STATE_WARNING);
        } else {
            $this->setState(StateEnumeration::STATE_ERROR);
        }

        return round($pruneRatio, 4);
    }

    protected function getQueryCacheHelper(): QueryCacheHelper
    {
        return GeneralUtility::makeInstance(QueryCacheHelper::class);
    }
}

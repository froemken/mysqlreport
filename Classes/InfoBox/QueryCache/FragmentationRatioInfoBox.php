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
 * InfoBox to inform about current query cache fragmentation ratio
 */
class FragmentationRatioInfoBox extends AbstractInfoBox
{
    protected $pageIdentifier = 'queryCache';

    protected $title = 'Fragmentation Ratio';

    public function renderBody(Page $page): string
    {
        if (
            !isset($page->getStatusValues()['Qcache_total_blocks'])
            || (int)$page->getStatusValues()['Qcache_total_blocks'] === 0
            || !$this->getQueryCacheHelper()->isQueryCacheEnabled($page)
        ) {
            $this->shouldBeRendered = false;
            return '';
        }

        $content = [];
        $content[] = 'You can defragment query cache with FLUSH QUERY CACHE if you are allowed to (RELOAD privileges).';
        $content[] = 'While processing FLUSH, query cache is not available and all queries will be blocked for that duration.';
        $content[] = 'But this command is very fast if your query_cache_size is small.';
        $content[] = 'If you want to clear query cache you should use: RESET QUERY CACHE';
        $content[] = "\n\n";
        $content[] = 'Fragmentation Ratio: %f%%';

        return sprintf(
            implode(' ', $content),
            $this->getFragmentationRatio($page->getStatusValues())
        );
    }

    protected function getFragmentationRatio(StatusValues $status): float
    {
        // total blocks / 2 = maximum fragmentation
        $fragmentation = ($status['Qcache_free_blocks'] / ($status['Qcache_total_blocks'] / 2)) * 100;
        if ($fragmentation <= 15) {
            $this->setState(StateEnumeration::STATE_OK);
        } elseif ($fragmentation <= 25) {
            $this->setState(StateEnumeration::STATE_WARNING);
        } else {
            $this->setState(StateEnumeration::STATE_ERROR);
        }

        return round($fragmentation, 4);
    }

    protected function getQueryCacheHelper(): QueryCacheHelper
    {
        return GeneralUtility::makeInstance(QueryCacheHelper::class);
    }
}

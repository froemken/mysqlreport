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
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\Helper\QueryCacheHelper;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\Menu\Page;

/**
 * InfoBox to inform about current query cache average query size
 */
class AverageQuerySizeInfoBox extends AbstractInfoBox
{
    protected $pageIdentifier = 'queryCache';

    protected $title = 'Average Query Size';

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
            !isset($page->getStatusValues()['Qcache_queries_in_cache'])
            || (int)$page->getStatusValues()['Qcache_queries_in_cache'] === 0
            || !$this->queryCacheHelper->isQueryCacheEnabled($page)
        ) {
            $this->shouldBeRendered = false;
            return '';
        }

        $content = [];
        $content[] = 'Average Query Size of %f Bytes should not be higher than %d (query_cache_min_res_unit).';

        return sprintf(
            implode(' ', $content),
            $this->getAvgQuerySize($page->getStatusValues(), $page->getVariables()),
            $page->getVariables()['query_cache_min_res_unit']
        );
    }

    protected function getAvgQuerySize(StatusValues $status, Variables $variables): float
    {
        $avgQuerySize = 0;
        if ($status['Qcache_queries_in_cache']) {
            $avgQuerySize = $this->getUsedQueryCacheSize($status, $variables) / $status['Qcache_queries_in_cache'];
        }

        if ($avgQuerySize > $variables['query_cache_min_res_unit']) {
            $this->setState(StateEnumeration::STATE_ERROR);
        } else {
            $this->setState(StateEnumeration::STATE_OK);
        }

        return round($avgQuerySize, 4);
    }

    protected function getUsedQueryCacheSize(StatusValues $status, Variables $variables): int
    {
        // ~40KB are reserved by operating system
        $queryCacheSize = $variables['query_cache_size'] - (40 * 1024);

        return $queryCacheSize - $status['Qcache_free_memory'];
    }
}

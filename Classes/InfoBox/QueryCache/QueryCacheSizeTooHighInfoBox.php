<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\QueryCache;

use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\Helper\QueryCacheHelper;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\Menu\Page;

/**
 * InfoBox to inform about current query cache too high
 */
class QueryCacheSizeTooHighInfoBox extends AbstractInfoBox
{
    protected string $pageIdentifier = 'queryCache';

    protected string $title = 'Query Cache too high';

    private QueryCacheHelper $queryCacheHelper;

    public function injectQueryCacheHelper(QueryCacheHelper $queryCacheHelper): void
    {
        $this->queryCacheHelper = $queryCacheHelper;
    }

    public function renderBody(Page $page): string
    {
        if (
            !isset($page->getVariables()['query_cache_size'])
            || !$this->queryCacheHelper->isQueryCacheEnabled($page)
        ) {
            $this->shouldBeRendered = false;
            return '';
        }

        if ($page->getVariables()['query_cache_size'] < 268435456) {
            $this->shouldBeRendered = false;
            return '';
        }

        $this->setState(StateEnumeration::STATE_ERROR);

        $content = [];
        $content[] = 'The query cache is configured too high.';
        $content[] = 'Because of concurrency it may take too long to delete queries from Query Cache.';
        $content[] = 'Try to keep query cache below 256MBAs higher as better.';

        return implode(' ', $content);
    }
}

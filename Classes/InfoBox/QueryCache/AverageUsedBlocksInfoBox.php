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
 * InfoBox to inform about current query cache average used blocks
 */
class AverageUsedBlocksInfoBox extends AbstractInfoBox
{
    protected $pageIdentifier = 'queryCache';

    protected $title = 'Average Used Blocks';

    public function renderBody(Page $page): string
    {
        if (!$this->getQueryCacheHelper()->isQueryCacheEnabled($page)) {
            $this->shouldBeRendered = false;
            return '';
        }

        $content = [];
        $content[] = 'Currently your server is configured with an average amount of %f used blocks in Query Cache.';
        $content[] = 'Please adjust query_cache_limit of currently %d Bytes to your needs, if needed';

        $this->addUnorderedListEntry('16KB - 128KB', 'very small');
        $this->addUnorderedListEntry('128KB - 256KB', 'small');
        $this->addUnorderedListEntry('256KB - 1MB', 'medium');
        $this->addUnorderedListEntry('1MB - 4MB', 'big');

        return sprintf(
            implode(' ', $content),
            $this->getAvgUsedBlocks($page->getStatusValues()),
            $page->getVariables()['query_cache_limit']
        );
    }

    /**
     * get average used blocks each query
     * this can indicate if you use more small or big queries
     * Quote from link: Every cached query requires a minimum of two blocks (one for the query text and one or more for the query results).
     *
     * @link: http://dev.mysql.com/doc/refman/5.0/en/query-cache-status-and-maintenance.html
     */
    protected function getAvgUsedBlocks(StatusValues $status): float
    {
        $avgUsedBlocks = 0;
        $usedBlocks = $status['Qcache_total_blocks'] - $status['Qcache_free_blocks'];
        $minimumUsedBlocks = $status['Qcache_queries_in_cache'] * 2; // see link above
        if ($minimumUsedBlocks) {
            $avgUsedBlocks = $usedBlocks / $minimumUsedBlocks;
        }

        return round($avgUsedBlocks, 4);
    }

    protected function getQueryCacheHelper(): QueryCacheHelper
    {
        return GeneralUtility::makeInstance(QueryCacheHelper::class);
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\QueryCache;

use StefanFroemken\Mysqlreport\Helper\QueryCacheHelper;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxUnorderedListInterface;
use StefanFroemken\Mysqlreport\InfoBox\ListElement;
use StefanFroemken\Mysqlreport\Traits\GetStatusValuesAndVariablesTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about the current query cache average used blocks
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
)]
class AverageUsedBlocksInfoBox extends AbstractInfoBox implements InfoBoxUnorderedListInterface
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Average Used Blocks';

    private QueryCacheHelper $queryCacheHelper;

    public function injectQueryCacheHelper(QueryCacheHelper $queryCacheHelper): void
    {
        $this->queryCacheHelper = $queryCacheHelper;
    }

    public function renderBody(): string
    {
        if (
            !isset($this->getStatusValues()['Qcache_queries_in_cache'])
            || (int)$this->getStatusValues()['Qcache_queries_in_cache'] === 0
            || !$this->queryCacheHelper->isQueryCacheEnabled($this->getVariables())
        ) {
            return '';
        }

        $content = [];
        $content[] = 'Currently your server is configured with an average amount of %f used blocks in Query Cache.';
        $content[] = 'Please adjust query_cache_limit of currently %d Bytes to your needs, if needed';

        return sprintf(
            implode(' ', $content),
            $this->getAvgUsedBlocks(),
            $this->getVariables()['query_cache_limit'],
        );
    }

    /**
     * get average used blocks each query
     * this can indicate if you use more small or big queries
     * Quote from link: Every cached query requires a minimum of two blocks (one for the query text and one or more for the query results).
     *
     * @link: http://dev.mysql.com/doc/refman/5.0/en/query-cache-status-and-maintenance.html
     */
    protected function getAvgUsedBlocks(): float
    {
        $status = $this->getStatusValues();

        $avgUsedBlocks = 0;
        $usedBlocks = $status['Qcache_total_blocks'] - $status['Qcache_free_blocks'];
        $minimumUsedBlocks = $status['Qcache_queries_in_cache'] * 2; // see link above
        if ($minimumUsedBlocks) {
            $avgUsedBlocks = $usedBlocks / $minimumUsedBlocks;
        }

        return round($avgUsedBlocks, 4);
    }

    /**
     * @return \SplQueue<ListElement>
     */
    public function getUnorderedList(): \SplQueue
    {
        $unorderedList = new \SplQueue();

        $unorderedList->enqueue(new ListElement(
            title: 'very small',
            value: '16KB - 128KB',
        ));
        $unorderedList->enqueue(new ListElement(
            title: 'small',
            value: '128KB - 256KB',
        ));
        $unorderedList->enqueue(new ListElement(
            title: 'medium',
            value: '256KB - 1MB',
        ));
        $unorderedList->enqueue(new ListElement(
            title: 'big',
            value: '1MB - 4MB',
        ));

        return $unorderedList;
    }
}

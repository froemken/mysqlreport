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
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use StefanFroemken\Mysqlreport\Traits\GetStatusValuesAndVariablesTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about the current query cache fragmentation ratio
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
)]
class FragmentationRatioInfoBox extends AbstractInfoBox implements InfoBoxStateInterface
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Fragmentation Ratio';

    private QueryCacheHelper $queryCacheHelper;

    public function injectQueryCacheHelper(QueryCacheHelper $queryCacheHelper): void
    {
        $this->queryCacheHelper = $queryCacheHelper;
    }

    public function renderBody(): string
    {
        if (
            !isset($this->getStatusValues()['Qcache_total_blocks'])
            || (int)$this->getStatusValues()['Qcache_total_blocks'] === 0
            || !$this->queryCacheHelper->isQueryCacheEnabled($this->getVariables())
        ) {
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
            $this->getFragmentationRatio(),
        );
    }

    protected function getFragmentationRatio(): float
    {
        $status = $this->getStatusValues();

        // total blocks / 2 = maximum fragmentation
        $fragmentation = ($status['Qcache_free_blocks'] / ($status['Qcache_total_blocks'] / 2)) * 100;

        return round($fragmentation, 4);
    }

    public function getState(): StateEnumeration
    {
        $fragmentation = $this->getFragmentationRatio();
        if ($fragmentation <= 15) {
            $state = StateEnumeration::STATE_OK;
        } elseif ($fragmentation <= 25) {
            $state = StateEnumeration::STATE_WARNING;
        } else {
            $state = StateEnumeration::STATE_ERROR;
        }

        return $state;
    }
}

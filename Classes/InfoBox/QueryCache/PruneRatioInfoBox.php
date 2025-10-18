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
 * InfoBox to inform about current query cache prune ratio
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
    attributes: ['priority' => 80],
)]
class PruneRatioInfoBox extends AbstractInfoBox implements InfoBoxStateInterface
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Prune Ratio';

    private QueryCacheHelper $queryCacheHelper;

    public function injectQueryCacheHelper(QueryCacheHelper $queryCacheHelper): void
    {
        $this->queryCacheHelper = $queryCacheHelper;
    }

    public function renderBody(): string
    {
        if (
            !isset($this->getStatusValues()['Qcache_inserts'])
            || (int)$this->getStatusValues()['Qcache_inserts'] === 0
            || !$this->queryCacheHelper->isQueryCacheEnabled($this->getVariables())
        ) {
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
            $this->getPruneRatio(),
        );
    }

    protected function getPruneRatio(): float
    {
        $status = $this->getStatusValues();
        $pruneRatio = 0;

        if ($status['Qcache_inserts']) {
            $pruneRatio = ($status['Qcache_lowmem_prunes'] / $status['Qcache_inserts']) * 100;
        }

        return round($pruneRatio, 4);
    }

    public function getState(): StateEnumeration
    {
        $pruneRatio = $this->getPruneRatio();
        if ($pruneRatio <= 10) {
            $state = StateEnumeration::STATE_OK;
        } elseif ($pruneRatio <= 40) {
            $state = StateEnumeration::STATE_WARNING;
        } else {
            $state = StateEnumeration::STATE_ERROR;
        }

        return $state;
    }
}

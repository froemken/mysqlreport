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
 * InfoBox to inform about current query cache average query size
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
)]
class AverageQuerySizeInfoBox extends AbstractInfoBox implements InfoBoxStateInterface
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Average Query Size';

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
        $content[] = 'Average Query Size of %f Bytes should not be higher than %d (query_cache_min_res_unit).';

        return sprintf(
            implode(' ', $content),
            $this->getAvgQuerySize(),
            $this->getVariables()['query_cache_min_res_unit'],
        );
    }

    protected function getAvgQuerySize(): float
    {
        $status = $this->getStatusValues();

        $avgQuerySize = 0;
        if ($status['Qcache_queries_in_cache']) {
            $avgQuerySize = $this->getUsedQueryCacheSize() / $status['Qcache_queries_in_cache'];
        }

        return round($avgQuerySize, 4);
    }

    protected function getUsedQueryCacheSize(): int
    {
        $status = $this->getStatusValues();
        $variables = $this->getVariables();

        // ~40KB are reserved by the operating system
        $queryCacheSize = $variables['query_cache_size'] - (40 * 1024);

        return $queryCacheSize - $status['Qcache_free_memory'];
    }

    public function getState(): StateEnumeration
    {
        $variables = $this->getVariables();

        $avgQuerySize = $this->getAvgQuerySize();
        if ($avgQuerySize > $variables['query_cache_min_res_unit']) {
            $state = StateEnumeration::STATE_ERROR;
        } else {
            $state = StateEnumeration::STATE_OK;
        }

        return $state;
    }
}

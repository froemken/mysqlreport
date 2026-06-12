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
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about current query cache average query size
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
)]
final readonly class AverageQuerySizeInfoBox implements InfoBoxInterface, InfoBoxStateInterface
{
    public const TITLE = 'Average Query Size';

    public function __construct(
        private StatusValues $statusValues,
        private Variables $variables,
        private QueryCacheHelper $queryCacheHelper,
    ) {}

    public function getBody(): string
    {
        if (
            !isset($this->statusValues['Qcache_queries_in_cache'])
            || (int)$this->statusValues['Qcache_queries_in_cache'] === 0
            || !$this->queryCacheHelper->isQueryCacheEnabled($this->variables)
        ) {
            return '';
        }

        $content = [];
        $content[] = 'Average Query Size of %f Bytes should not be higher than %d (query_cache_min_res_unit).';

        return sprintf(
            implode(' ', $content),
            $this->getAvgQuerySize(),
            $this->variables['query_cache_min_res_unit'],
        );
    }

    private function getAvgQuerySize(): float
    {
        $status = $this->statusValues;

        $avgQuerySize = 0;
        if ($status['Qcache_queries_in_cache']) {
            $avgQuerySize = $this->getUsedQueryCacheSize() / $status['Qcache_queries_in_cache'];
        }

        return round($avgQuerySize, 4);
    }

    private function getUsedQueryCacheSize(): int
    {
        $status = $this->statusValues;
        $variables = $this->variables;

        // ~40KB are reserved by the operating system
        $queryCacheSize = $variables['query_cache_size'] - (40 * 1024);

        return $queryCacheSize - $status['Qcache_free_memory'];
    }

    public function getState(): StateEnumeration
    {
        $variables = $this->variables;

        $avgQuerySize = $this->getAvgQuerySize();
        if ($avgQuerySize > $variables['query_cache_min_res_unit']) {
            return StateEnumeration::STATE_ERROR;
        }

        return StateEnumeration::STATE_OK;
    }
}

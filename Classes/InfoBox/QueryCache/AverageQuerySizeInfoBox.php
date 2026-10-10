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
use StefanFroemken\Mysqlreport\Helper\QueryCacheHelper;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;

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
        private QueryCacheHelper $queryCacheHelper,
    ) {}

    public function getBody(StatusValues $statusValues, Variables $variables): string
    {
        if (
            !isset($statusValues['Qcache_queries_in_cache'])
            || (int)$statusValues['Qcache_queries_in_cache'] === 0
            || !$this->queryCacheHelper->isQueryCacheEnabled($variables)
        ) {
            return '';
        }

        $content = [];
        $content[] = 'Average Query Size of %f Bytes should not be higher than %d (query_cache_min_res_unit).';

        return sprintf(
            implode(' ', $content),
            $this->getAvgQuerySize($statusValues, $variables),
            $variables['query_cache_min_res_unit'],
        );
    }

    private function getAvgQuerySize(StatusValues $statusValues, Variables $variables): float
    {
        $avgQuerySize = 0;
        if ($statusValues['Qcache_queries_in_cache']) {
            $avgQuerySize = $this->getUsedQueryCacheSize($statusValues, $variables) / $statusValues['Qcache_queries_in_cache'];
        }

        return round($avgQuerySize, 4);
    }

    private function getUsedQueryCacheSize(StatusValues $statusValues, Variables $variables): int
    {
        // ~40KB are reserved by the operating system
        $queryCacheSize = $variables['query_cache_size'] - (40 * 1024);

        return $queryCacheSize - $statusValues['Qcache_free_memory'];
    }

    public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
    {
        $avgQuerySize = $this->getAvgQuerySize($statusValues, $variables);
        if ($avgQuerySize > $variables['query_cache_min_res_unit']) {
            return ContextualFeedbackSeverity::ERROR;
        }

        return ContextualFeedbackSeverity::OK;
    }
}

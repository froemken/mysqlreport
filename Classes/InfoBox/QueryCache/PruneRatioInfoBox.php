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
 * InfoBox to inform about current query cache prune ratio
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
    attributes: ['priority' => 80],
)]
final readonly class PruneRatioInfoBox implements InfoBoxInterface, InfoBoxStateInterface
{
    public function __construct(
        private QueryCacheHelper $queryCacheHelper,
    ) {}

    public function getTitle(): string
    {
        return 'Prune Ratio';
    }

    public function getBody(StatusValues $statusValues, Variables $variables): string
    {
        if (
            !isset($statusValues['Qcache_inserts'])
            || (int)$statusValues['Qcache_inserts'] === 0
            || !$this->queryCacheHelper->isQueryCacheEnabled($variables)
        ) {
            return '';
        }

        $content = [
            'This is the ratio between deleted and inserted queries in cache.',
            'If a new query was written into Cache and prune increases, it may indicate that query_cache_size is too small.',
            'A maximum of 256MB should be OK. If ratio is still high check query_cache_limit and section "Average used blocks".',
            'If ratio is still too high deactivate Query Cache completely due to mutex/concurrency overhead.',
            "\n\n",
            'Prune Ratio: %f%%',
        ];

        return sprintf(
            implode(' ', $content),
            $this->getPruneRatio($statusValues),
        );
    }

    private function getPruneRatio(StatusValues $statusValues): float
    {
        $pruneRatio = 0.0;
        if ($statusValues['Qcache_inserts']) {
            $pruneRatio = ($statusValues['Qcache_lowmem_prunes'] / $statusValues['Qcache_inserts']) * 100;
        }

        return round($pruneRatio, 4);
    }

    public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
    {
        $pruneRatio = $this->getPruneRatio($statusValues);
        if ($pruneRatio <= 10) {
            $state = ContextualFeedbackSeverity::OK;
        } elseif ($pruneRatio <= 40) {
            $state = ContextualFeedbackSeverity::WARNING;
        } else {
            $state = ContextualFeedbackSeverity::ERROR;
        }

        return $state;
    }
}

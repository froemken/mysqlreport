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
 * InfoBox to inform about the current query cache fragmentation ratio
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
)]
final readonly class FragmentationRatioInfoBox implements InfoBoxInterface, InfoBoxStateInterface
{
    public function __construct(
        private QueryCacheHelper $queryCacheHelper,
    ) {}

    public function getTitle(): string
    {
        return 'Fragmentation Ratio';
    }

    public function getBody(StatusValues $statusValues, Variables $variables): string
    {
        if (
            !isset($statusValues['Qcache_total_blocks'])
            || (int)$statusValues['Qcache_total_blocks'] === 0
            || !$this->queryCacheHelper->isQueryCacheEnabled($variables)
        ) {
            return '';
        }

        $content = [
            'You can defragment query cache with FLUSH QUERY CACHE if you are allowed to (RELOAD privileges).',
            'While processing FLUSH, query cache is not available and all queries will be blocked for that duration.',
            'But this command is very fast if your query_cache_size is small.',
            'If you want to clear query cache you should use: RESET QUERY CACHE',
            "\n\n",
            'Fragmentation Ratio: %f%%',
        ];

        return sprintf(
            implode(' ', $content),
            $this->getFragmentationRatio($statusValues),
        );
    }

    private function getFragmentationRatio(StatusValues $statusValues): float
    {
        // total blocks / 2 = maximum fragmentation
        $fragmentation = ($statusValues['Qcache_free_blocks'] / ($statusValues['Qcache_total_blocks'] / 2)) * 100;

        return round($fragmentation, 4);
    }

    public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
    {
        $fragmentation = $this->getFragmentationRatio($statusValues);
        if ($fragmentation <= 15) {
            $state = ContextualFeedbackSeverity::OK;
        } elseif ($fragmentation <= 25) {
            $state = ContextualFeedbackSeverity::WARNING;
        } else {
            $state = ContextualFeedbackSeverity::ERROR;
        }

        return $state;
    }
}

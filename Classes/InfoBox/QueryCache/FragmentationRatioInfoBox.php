<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\QueryCache;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\Helper\QueryCacheHelper;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxAvailabilityInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;

/**
 * InfoBox to inform about the current query cache fragmentation ratio.
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
)]
final readonly class FragmentationRatioInfoBox implements InfoBoxInterface, InfoBoxStateInterface, InfoBoxAvailabilityInterface
{
    public function __construct(
        private QueryCacheHelper $queryCacheHelper,
    ) {}

    public function getTitle(): string
    {
        return 'Fragmentation Ratio';
    }

    public function isAvailable(
        AbstractPlatform $platform,
        Variables $variables,
        StatusValues $statusValues,
    ): bool {
        return $this->queryCacheHelper->isQueryCacheEnabled($variables);
    }

    public function getBody(StatusValues $statusValues, Variables $variables): string
    {
        $totalBlocks = (int)($statusValues['Qcache_total_blocks'] ?? 0);
        $freeBlocks = (int)($statusValues['Qcache_free_blocks'] ?? 0);

        if ($totalBlocks === 0) {
            return '';
        }

        $fragmentation = $this->calculateFragmentation($freeBlocks, $totalBlocks);

        $paragraphs = [
            sprintf('The current Query Cache memory fragmentation ratio is %.2f%%.', $fragmentation),
            'When memory is fragmented, queries may be pruned prematurely even if overall free memory is available.',
            'You can defragment the cache with "FLUSH QUERY CACHE". Note that this locks the cache mutex temporarily.',
        ];

        return implode("\n\n", $paragraphs);
    }

    public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
    {
        $totalBlocks = (int)($statusValues['Qcache_total_blocks'] ?? 0);
        $freeBlocks = (int)($statusValues['Qcache_free_blocks'] ?? 0);
        $fragmentation = $this->calculateFragmentation($freeBlocks, $totalBlocks);

        if ($fragmentation <= 15.0) {
            return ContextualFeedbackSeverity::OK;
        }

        if ($fragmentation <= 25.0) {
            return ContextualFeedbackSeverity::WARNING;
        }

        return ContextualFeedbackSeverity::ERROR;
    }

    private function calculateFragmentation(int $freeBlocks, int $totalBlocks): float
    {
        if ($totalBlocks <= 0) {
            return 0.0;
        }

        $maxFragmentedBlocks = $totalBlocks / 2;
        if ($maxFragmentedBlocks <= 0) {
            return 0.0;
        }

        return round(($freeBlocks / $maxFragmentedBlocks) * 100, 2);
    }
}

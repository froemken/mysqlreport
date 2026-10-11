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
 * InfoBox to inform about the current query cache hit ratio.
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
    attributes: ['priority' => 80],
)]
final readonly class HitRatioInfoBox implements InfoBoxInterface, InfoBoxStateInterface, InfoBoxAvailabilityInterface
{
    public function __construct(
        private QueryCacheHelper $queryCacheHelper,
    ) {}

    public function getTitle(): string
    {
        return 'Hit Ratio';
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
        $hits = (int)($statusValues['Qcache_hits'] ?? 0);
        $selects = (int)($statusValues['Com_select'] ?? 0);
        $total = $hits + $selects;

        if ($total === 0) {
            return '';
        }

        $hitRatio = $this->calculateHitRatio($hits, $total);

        $paragraphs = [
            sprintf('The current Query Cache hit ratio is %.2f%%.', $hitRatio),
            sprintf('Formula: Qcache_hits (%d) / (Qcache_hits (%d) + Com_select (%d)) * 100', $hits, $hits, $selects),
            $this->getAdviceForHitRatio($hitRatio),
        ];

        return implode("\n\n", $paragraphs);
    }

    public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
    {
        $hits = (int)($statusValues['Qcache_hits'] ?? 0);
        $total = $hits + (int)($statusValues['Com_select'] ?? 0);
        $hitRatio = $this->calculateHitRatio($hits, $total);

        if ($hitRatio >= 50.0) {
            return ContextualFeedbackSeverity::OK;
        }

        if ($hitRatio >= 25.0) {
            return ContextualFeedbackSeverity::WARNING;
        }

        return ContextualFeedbackSeverity::ERROR;
    }

    private function calculateHitRatio(int $hits, int $total): float
    {
        if ($total <= 0) {
            return 0.0;
        }

        return round(($hits / $total) * 100, 2);
    }

    private function getAdviceForHitRatio(float $hitRatio): string
    {
        if ($hitRatio >= 50.0) {
            return 'A hit ratio above 50% indicates that the cache effectively serves a significant portion of read queries.';
        }

        if ($hitRatio >= 25.0) {
            return 'A hit ratio between 25% and 50% offers moderate benefit. Observe whether write queries frequently invalidate entries.';
        }

        return 'A hit ratio below 25% indicates that most queries bypass the cache. The mutex overhead of cache management '
            . 'likely exceeds any performance gains. Consider disabling the Query Cache.';
    }
}

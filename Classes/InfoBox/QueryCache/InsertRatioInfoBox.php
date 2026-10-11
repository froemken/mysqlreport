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
 * InfoBox to inform about the current query cache insert ratio.
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
    attributes: ['priority' => 80],
)]
final readonly class InsertRatioInfoBox implements InfoBoxInterface, InfoBoxStateInterface, InfoBoxAvailabilityInterface
{
    public function __construct(
        private QueryCacheHelper $queryCacheHelper,
    ) {}

    public function getTitle(): string
    {
        return 'Insert Ratio';
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
        $inserts = (int)($statusValues['Qcache_inserts'] ?? 0);
        $hits = (int)($statusValues['Qcache_hits'] ?? 0);
        $total = $hits + (int)($statusValues['Com_select'] ?? 0);

        if ($total === 0 || $inserts === 0) {
            return '';
        }

        $insertRatio = $this->calculateInsertRatio($inserts, $total);

        $paragraphs = [
            sprintf('The current Query Cache insert ratio is %.2f%%.', $insertRatio),
            sprintf('Formula: Qcache_inserts (%d) / Total Selects (%d) * 100', $inserts, $total),
            $this->getAdviceForInsertRatio($insertRatio, $hits, $inserts),
        ];

        return implode("\n\n", $paragraphs);
    }

    public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
    {
        $inserts = (int)($statusValues['Qcache_inserts'] ?? 0);
        $hits = (int)($statusValues['Qcache_hits'] ?? 0);
        $total = $hits + (int)($statusValues['Com_select'] ?? 0);
        $insertRatio = $this->calculateInsertRatio($inserts, $total);

        if ($insertRatio <= 25.0) {
            return ContextualFeedbackSeverity::OK;
        }

        if ($insertRatio <= 50.0) {
            return ContextualFeedbackSeverity::WARNING;
        }

        return ContextualFeedbackSeverity::ERROR;
    }

    private function calculateInsertRatio(int $inserts, int $total): float
    {
        if ($total <= 0) {
            return 0.0;
        }

        return round(($inserts / $total) * 100, 2);
    }

    private function getAdviceForInsertRatio(float $insertRatio, int $hits, int $inserts): string
    {
        if ($inserts > $hits && $hits > 0) {
            return 'Queries are inserted more frequently than they are retrieved from cache. '
                . 'This indicates high churn with unique queries that are rarely reused.';
        }

        if ($insertRatio <= 25.0) {
            return 'Insert ratio is well-balanced. New queries are cached without overburdening the cache subsystem.';
        }

        return 'A high insert ratio indicates frequent query registration overhead. '
            . 'If hit ratio is not significantly higher than insert ratio, caching may degrade performance.';
    }
}

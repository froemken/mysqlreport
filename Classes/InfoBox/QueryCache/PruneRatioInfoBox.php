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
 * InfoBox to inform about the current query cache prune ratio.
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
    attributes: ['priority' => 80],
)]
final readonly class PruneRatioInfoBox implements InfoBoxInterface, InfoBoxStateInterface, InfoBoxAvailabilityInterface
{
    public function __construct(
        private QueryCacheHelper $queryCacheHelper,
    ) {}

    public function getTitle(): string
    {
        return 'Prune Ratio';
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
        $prunes = (int)($statusValues['Qcache_lowmem_prunes'] ?? 0);

        if ($inserts === 0) {
            return '';
        }

        $pruneRatio = $this->calculatePruneRatio($prunes, $inserts);

        $paragraphs = [
            sprintf('The current Query Cache prune ratio is %.2f%%.', $pruneRatio),
            sprintf('Formula: Qcache_lowmem_prunes (%d) / Qcache_inserts (%d) * 100', $prunes, $inserts),
            $this->getAdviceForPruneRatio($pruneRatio),
        ];

        return implode("\n\n", $paragraphs);
    }

    public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
    {
        $inserts = (int)($statusValues['Qcache_inserts'] ?? 0);
        $prunes = (int)($statusValues['Qcache_lowmem_prunes'] ?? 0);
        $pruneRatio = $this->calculatePruneRatio($prunes, $inserts);

        if ($pruneRatio <= 15.0) {
            return ContextualFeedbackSeverity::OK;
        }

        if ($pruneRatio <= 35.0) {
            return ContextualFeedbackSeverity::WARNING;
        }

        return ContextualFeedbackSeverity::ERROR;
    }

    private function calculatePruneRatio(int $prunes, int $inserts): float
    {
        if ($inserts <= 0) {
            return 0.0;
        }

        return round(($prunes / $inserts) * 100, 2);
    }

    private function getAdviceForPruneRatio(float $pruneRatio): string
    {
        if ($pruneRatio <= 15.0) {
            return 'A low prune ratio indicates sufficient cache memory for the current query working set.';
        }

        if ($pruneRatio <= 35.0) {
            return 'Moderate prune activity. Queries are occasionally evicted due to memory pressure before being reused.';
        }

        return 'A high prune ratio indicates severe memory starvation or high cache churn. Queries are frequently evicted '
            . 'shortly after insertion. If query_cache_size cannot be raised up to 128 MB, disable the Query Cache.';
    }
}

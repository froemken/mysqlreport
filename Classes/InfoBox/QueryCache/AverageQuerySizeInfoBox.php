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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * InfoBox to inform about the average query size in Query Cache.
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
)]
final readonly class AverageQuerySizeInfoBox implements InfoBoxInterface, InfoBoxStateInterface, InfoBoxAvailabilityInterface
{
    public function __construct(
        private QueryCacheHelper $queryCacheHelper,
    ) {}

    public function getTitle(): string
    {
        return 'Average Query Size';
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
        $queriesInCache = (int)($statusValues['Qcache_queries_in_cache'] ?? 0);
        if ($queriesInCache === 0) {
            return '';
        }

        $avgSize = $this->calculateAverageQuerySize($statusValues, $variables, $queriesInCache);
        $minResUnit = (int)($variables['query_cache_min_res_unit'] ?? 4096);

        $paragraphs = [
            sprintf(
                'Average query size in cache is %s (query_cache_min_res_unit: %s).',
                GeneralUtility::formatSize((int)$avgSize),
                GeneralUtility::formatSize($minResUnit),
            ),
            'Each cached query allocates memory in blocks of query_cache_min_res_unit. '
            . 'If your average query size is much smaller, reducing this setting saves memory and reduces fragmentation.',
        ];

        return implode("\n\n", $paragraphs);
    }

    public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
    {
        $queriesInCache = (int)($statusValues['Qcache_queries_in_cache'] ?? 0);
        $avgSize = $this->calculateAverageQuerySize($statusValues, $variables, $queriesInCache);
        $minResUnit = (int)($variables['query_cache_min_res_unit'] ?? 4096);

        if ($avgSize > $minResUnit) {
            return ContextualFeedbackSeverity::INFO;
        }

        return ContextualFeedbackSeverity::OK;
    }

    private function calculateAverageQuerySize(StatusValues $statusValues, Variables $variables, int $queriesInCache): float
    {
        if ($queriesInCache <= 0) {
            return 0.0;
        }

        $totalSize = (int)($variables['query_cache_size'] ?? 0) - (40 * 1024);
        $freeMemory = (int)($statusValues['Qcache_free_memory'] ?? 0);
        $usedSize = max(0, $totalSize - $freeMemory);

        return round($usedSize / $queriesInCache, 2);
    }
}

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
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxUnorderedListInterface;
use StefanFroemken\Mysqlreport\InfoBox\ListElement;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * InfoBox to inform about the average used blocks per query in Query Cache.
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
)]
final readonly class AverageUsedBlocksInfoBox implements InfoBoxInterface, InfoBoxUnorderedListInterface, InfoBoxAvailabilityInterface
{
    public function __construct(
        private QueryCacheHelper $queryCacheHelper,
    ) {}

    public function getTitle(): string
    {
        return 'Average Used Blocks';
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

        $avgUsedBlocks = $this->calculateAvgUsedBlocks($statusValues, $queriesInCache);
        $limit = GeneralUtility::formatSize((int)($variables['query_cache_limit'] ?? 1048576));

        $paragraphs = [
            sprintf('Average used blocks per cached query: %.2f.', $avgUsedBlocks),
            sprintf('Current query_cache_limit is %s (queries exceeding this size are not cached).', $limit),
            'Every cached query requires a minimum of two blocks (one for query text and one or more for query results).',
        ];

        return implode("\n\n", $paragraphs);
    }

    /**
     * @return ListElement[]
     */
    public function getUnorderedList(StatusValues $statusValues, Variables $variables): array
    {
        return [
            new ListElement(title: 'Very small result sets', value: '16 KB - 128 KB'),
            new ListElement(title: 'Small result sets', value: '128 KB - 256 KB'),
            new ListElement(title: 'Medium result sets', value: '256 KB - 1 MB'),
            new ListElement(title: 'Large result sets', value: '1 MB - 4 MB'),
        ];
    }

    private function calculateAvgUsedBlocks(StatusValues $statusValues, int $queriesInCache): float
    {
        $usedBlocks = (int)($statusValues['Qcache_total_blocks'] ?? 0) - (int)($statusValues['Qcache_free_blocks'] ?? 0);
        $minimumUsedBlocks = $queriesInCache * 2;

        if ($minimumUsedBlocks <= 0) {
            return 0.0;
        }

        return round($usedBlocks / $minimumUsedBlocks, 2);
    }
}

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
 * InfoBox to warn when query_cache_size is configured higher than recommended.
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
)]
final readonly class QueryCacheSizeTooHighInfoBox implements InfoBoxInterface, InfoBoxStateInterface, InfoBoxAvailabilityInterface
{
    private const WARNING_THRESHOLD_BYTES = 134217728; // 128 MB
    private const ERROR_THRESHOLD_BYTES = 268435456;   // 256 MB

    public function __construct(
        private QueryCacheHelper $queryCacheHelper,
    ) {}

    public function getTitle(): string
    {
        return 'Query Cache Size Too High';
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
        $size = (int)($variables['query_cache_size'] ?? 0);
        if ($size <= self::WARNING_THRESHOLD_BYTES) {
            return '';
        }

        $formattedSize = GeneralUtility::formatSize($size);

        $paragraphs = [
            sprintf('The query_cache_size is configured to %s, which is higher than recommended.', $formattedSize),
            'Large Query Cache allocations significantly increase global mutex lock contention. When a table is modified, '
            . 'the server must scan and invalidate all corresponding queries while holding the lock.',
            'Recommended limit: Keep query_cache_size between 32 MB and 128 MB. If the active dataset requires more, '
            . 'it is typically more advantageous to disable the Query Cache entirely.',
        ];

        return implode("\n\n", $paragraphs);
    }

    public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
    {
        $size = (int)($variables['query_cache_size'] ?? 0);
        if ($size > self::ERROR_THRESHOLD_BYTES) {
            return ContextualFeedbackSeverity::ERROR;
        }

        return ContextualFeedbackSeverity::WARNING;
    }
}

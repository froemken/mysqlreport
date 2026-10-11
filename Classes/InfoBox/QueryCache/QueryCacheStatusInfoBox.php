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
 * InfoBox to inform about the current query cache status.
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
    attributes: ['priority' => 90],
)]
final readonly class QueryCacheStatusInfoBox implements InfoBoxInterface, InfoBoxStateInterface, InfoBoxAvailabilityInterface
{
    public function __construct(
        private QueryCacheHelper $queryCacheHelper,
    ) {}

    public function getTitle(): string
    {
        return 'Query Cache Status';
    }

    public function isAvailable(
        AbstractPlatform $platform,
        Variables $variables,
        StatusValues $statusValues,
    ): bool {
        return $this->queryCacheHelper->isQueryCacheSupported($variables);
    }

    public function getBody(StatusValues $statusValues, Variables $variables): string
    {
        $type = (string)($variables['query_cache_type'] ?? 'OFF');
        $formattedSize = GeneralUtility::formatSize((int)($variables['query_cache_size'] ?? 0));

        if (!$this->queryCacheHelper->isQueryCacheEnabled($variables)) {
            return $this->buildDisabledMessage($type, $formattedSize);
        }

        return $this->buildEnabledMessage($type, $formattedSize);
    }

    public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
    {
        if (!$this->queryCacheHelper->isQueryCacheEnabled($variables)) {
            return ContextualFeedbackSeverity::OK;
        }

        return ContextualFeedbackSeverity::INFO;
    }

    private function buildDisabledMessage(string $type, string $size): string
    {
        $paragraphs = [
            sprintf('The Query Cache is disabled (query_cache_type = %s, query_cache_size = %s).', $type, $size),
            'This is the recommended best practice on modern database systems, multi-core CPUs, NVMe/SSD storage, '
            . 'and shared or multi-tenant hosting environments.',
            'Disabling the Query Cache avoids global mutex lock contention during table write operations '
            . '(INSERT, UPDATE, DELETE). TYPO3 application-level caching and the InnoDB buffer pool provide superior '
            . 'performance without database-level mutex bottlenecks.',
        ];

        return implode("\n\n", $paragraphs);
    }

    private function buildEnabledMessage(string $type, string $size): string
    {
        $paragraphs = [
            sprintf('The Query Cache is enabled (query_cache_type = %s, query_cache_size = %s).', $type, $size),
            'Note: The Query Cache relies on a single global mutex lock. Any write to a cached table invalidates all '
            . 'cached queries for that table and may cause latency spikes under concurrent load.',
            'Review the metrics below. If you observe low hit ratios or high prune counts, consider disabling the '
            . 'Query Cache to prevent mutex contention.',
        ];

        return implode("\n\n", $paragraphs);
    }
}

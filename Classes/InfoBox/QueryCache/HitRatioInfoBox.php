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
 * InfoBox to inform about the current query cache hit ratio
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
    attributes: ['priority' => 80],
)]
final readonly class HitRatioInfoBox implements InfoBoxInterface, InfoBoxStateInterface
{
    public const TITLE = 'Hit Ratio';

    public function __construct(
        private QueryCacheHelper $queryCacheHelper,
    ) {}

    public function getBody(StatusValues $statusValues, Variables $variables): string
    {
        if (
            !isset($statusValues['Qcache_hits'])
            || (int)$statusValues['Qcache_hits'] === 0
            || !$this->queryCacheHelper->isQueryCacheEnabled($variables)
        ) {
            return '';
        }

        $content = [];
        $content[] = 'As higher as better.';
        $content[] = "\n\n";
        $content[] = 'Hit Ratio: %f';

        return sprintf(
            implode(' ', $content),
            $this->getHitRatio($statusValues),
        );
    }

    private function getHitRatio(StatusValues $statusValues): float
    {
        $hitRatio = ($statusValues['Qcache_hits'] / ($statusValues['Qcache_hits'] + $statusValues['Com_select'])) * 100;

        return round($hitRatio, 4);
    }

    public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
    {
        $hitRatio = $this->getHitRatio($statusValues);
        if ($hitRatio <= 20) {
            $state = ContextualFeedbackSeverity::ERROR;
        } elseif ($hitRatio <= 40) {
            $state = ContextualFeedbackSeverity::WARNING;
        } else {
            $state = ContextualFeedbackSeverity::OK;
        }

        return $state;
    }
}

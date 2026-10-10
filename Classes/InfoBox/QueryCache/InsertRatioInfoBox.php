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
 * InfoBox to inform about the current query cache insert ratio
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
    attributes: ['priority' => 80],
)]
final readonly class InsertRatioInfoBox implements InfoBoxInterface, InfoBoxStateInterface
{
    public function __construct(
        private QueryCacheHelper $queryCacheHelper,
    ) {}

    public function getTitle(): string
    {
        return 'Insert Ratio';
    }

    public function getBody(StatusValues $statusValues, Variables $variables): string
    {
        if (
            !isset($statusValues['Qcache_hits'])
            || (int)$statusValues['Qcache_hits'] === 0
            || !$this->queryCacheHelper->isQueryCacheEnabled($variables)
        ) {
            return '';
        }

        $content = [
            'This indicates if queries are written into cache very often.',
            'If you have just started your MySQL-Server or have less SELECT-Statement this value can be red or yellow.',
            'So please wait till all caches are warmed up.',
            "\n\n",
            'Insert Ratio: %f%%',
        ];

        return sprintf(
            implode(' ', $content),
            $this->getInsertRatio($statusValues),
        );
    }

    private function getInsertRatio(StatusValues $statusValues): float
    {
        $insertRatio = ($statusValues['Qcache_inserts'] / ($statusValues['Qcache_hits'] + $statusValues['Com_select'])) * 100;

        return round($insertRatio, 4);
    }

    public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
    {
        $insertRatio = $this->getInsertRatio($statusValues);
        if ($insertRatio <= 20) {
            $state = ContextualFeedbackSeverity::OK;
        } elseif ($insertRatio <= 40) {
            $state = ContextualFeedbackSeverity::WARNING;
        } else {
            $state = ContextualFeedbackSeverity::ERROR;
        }

        return $state;
    }
}

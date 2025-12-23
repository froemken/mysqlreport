<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\QueryCache;

use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\Helper\QueryCacheHelper;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use StefanFroemken\Mysqlreport\Traits\GetStatusValuesAndVariablesTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about the current query cache insert ratio
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
    attributes: ['priority' => 80],
)]
class InsertRatioInfoBox extends AbstractInfoBox implements InfoBoxStateInterface
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Insert Ratio';

    private QueryCacheHelper $queryCacheHelper;

    public function injectQueryCacheHelper(QueryCacheHelper $queryCacheHelper): void
    {
        $this->queryCacheHelper = $queryCacheHelper;
    }

    public function renderBody(): string
    {
        if (
            !isset($this->getStatusValues()['Qcache_hits'])
            || (int)$this->getStatusValues()['Qcache_hits'] === 0
            || !$this->queryCacheHelper->isQueryCacheEnabled($this->getVariables())
        ) {
            return '';
        }

        $content = [];
        $content[] = 'This indicates if queries are written into cache very often.';
        $content[] = 'If you have just started your MySQL-Server or have less SELECT-Statement this value can be red or yellow.';
        $content[] = 'So please wait till all caches are warmed up.';
        $content[] = "\n\n";
        $content[] = 'Insert Ratio: %f%%';

        return sprintf(
            implode(' ', $content),
            $this->getInsertRatio(),
        );
    }

    protected function getInsertRatio(): float
    {
        $status = $this->getStatusValues();

        $insertRatio = ($status['Qcache_inserts'] / ($status['Qcache_hits'] + $status['Com_select'])) * 100;

        return round($insertRatio, 4);
    }

    public function getState(): StateEnumeration
    {
        $insertRatio = $this->getInsertRatio();
        if ($insertRatio <= 20) {
            $state = StateEnumeration::STATE_OK;
        } elseif ($insertRatio <= 40) {
            $state = StateEnumeration::STATE_WARNING;
        } else {
            $state = StateEnumeration::STATE_ERROR;
        }

        return $state;
    }
}

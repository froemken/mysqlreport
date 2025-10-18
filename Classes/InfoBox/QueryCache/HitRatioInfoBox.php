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
 * InfoBox to inform about the current query cache hit ratio
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.query_cache',
    attributes: ['priority' => 80],
)]
class HitRatioInfoBox extends AbstractInfoBox implements InfoBoxStateInterface
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Hit Ratio';

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
        $content[] = 'As higher as better.';
        $content[] = "\n\n";
        $content[] = 'Hit Ratio: %f';

        return sprintf(
            implode(' ', $content),
            $this->getHitRatio(),
        );
    }

    protected function getHitRatio(): float
    {
        $status = $this->getStatusValues();

        $hitRatio = ($status['Qcache_hits'] / ($status['Qcache_hits'] + $status['Com_select'])) * 100;

        return round($hitRatio, 4);
    }

    public function getState(): StateEnumeration
    {
        $hitRatio = $this->getHitRatio();
        if ($hitRatio <= 20) {
            $state = StateEnumeration::STATE_ERROR;
        } elseif ($hitRatio <= 40) {
            $state = StateEnumeration::STATE_WARNING;
        } else {
            $state = StateEnumeration::STATE_OK;
        }

        return $state;
    }
}

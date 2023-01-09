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
use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\Helper\QueryCacheHelper;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\Menu\Page;

/**
 * InfoBox to inform about current query cache hit ratio
 */
class HitRatioInfoBox extends AbstractInfoBox
{
    protected string $pageIdentifier = 'queryCache';

    protected string $title = 'Hit Ratio';

    private QueryCacheHelper $queryCacheHelper;

    public function injectQueryCacheHelper(QueryCacheHelper $queryCacheHelper): void
    {
        $this->queryCacheHelper = $queryCacheHelper;
    }

    public function renderBody(Page $page): string
    {
        if (
            !isset($page->getStatusValues()['Qcache_hits'])
            || (int)$page->getStatusValues()['Qcache_hits'] === 0
            || !$this->queryCacheHelper->isQueryCacheEnabled($page)
        ) {
            $this->shouldBeRendered = false;
            return '';
        }

        $content = [];
        $content[] = 'As higher as better.';
        $content[] = "\n\n";
        $content[] = 'Hit Ratio: %f';

        return sprintf(
            implode(' ', $content),
            $this->getHitRatio($page->getStatusValues())
        );
    }

    protected function getHitRatio(StatusValues $status): float
    {
        $hitRatio = ($status['Qcache_hits'] / ($status['Qcache_hits'] + $status['Com_select'])) * 100;
        if ($hitRatio <= 20) {
            $this->setState(StateEnumeration::STATE_ERROR);
        } elseif ($hitRatio <= 40) {
            $this->setState(StateEnumeration::STATE_WARNING);
        } else {
            $this->setState(StateEnumeration::STATE_OK);
        }

        return round($hitRatio, 4);
    }
}

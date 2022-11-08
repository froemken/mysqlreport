<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\InnoDb;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\Menu\Page;

/**
 * InfoBox to inform about InnoDB buffer hit ratio
 */
class HitRatioInfoBox extends AbstractInfoBox
{
    protected $pageIdentifier = 'innoDb';

    protected $title = 'Hit Ratio';

    public function renderBody(Page $page): string
    {
        if (!isset($page->getStatusValues()['Innodb_page_size'])) {
            $this->shouldBeRendered = false;
            return '';
        }

        $content = [];
        $content[] = 'As higher as better.';
        $content[] = 'Everything below 90 is really bad. 99 maybe OK, but is not perfect. Everything above 99.9 is great.';
        $content[] = 'Increase the value of innodb_buffer_pool_size to get better results.';
        $content[] = "\n\n";
        $content[] = 'Hit Ratio: %f';

        return sprintf(
            implode(' ', $content),
            $this->getHitRatio($page->getStatusValues())
        );
    }

    /**
     * get hit ratio of innoDb Buffer
     * A ratio of 99.9 equals 1/1000
     */
    protected function getHitRatio(StatusValues $status): float
    {
        $hitRatio = ($status['Innodb_buffer_pool_read_requests'] / ($status['Innodb_buffer_pool_read_requests'] + $status['Innodb_buffer_pool_reads'])) * 100;
        if ($hitRatio <= 90) {
            $this->setState(StateEnumeration::STATE_ERROR);
        } elseif ($hitRatio <= 99.7) {
            $this->setState(StateEnumeration::STATE_WARNING);
        } else {
            $this->setState(StateEnumeration::STATE_OK);
        }

        return round($hitRatio, 2);
    }
}

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
 * InfoBox to inform about InnoDB buffer hit ratio by SF
 */
class HitRatioBySFInfoBox extends AbstractInfoBox
{
    protected string $pageIdentifier = 'innoDb';

    protected string $title = 'Hit Ratio by SF';

    public function renderBody(Page $page): string
    {
        if (
            !isset(
                $page->getStatusValues()['Innodb_page_size'],
                $page->getStatusValues()['Innodb_buffer_pool_reads'],
                $page->getStatusValues()['Innodb_buffer_pool_read_requests'],
            )
        ) {
            $this->shouldBeRendered = false;
            return '';
        }

        $content = [];
        $content[] = 'IMO we should have a factor of 1/1000 of speed between reading from HDD and reading from RAM.';
        $content[] = 'So, the amount of reading from HDD * 1000 would be our nice-to-have value for reading from RAM.';
        $content[] = '100 / value of nice-to-have * innodb_buffer_pool_read_requests shows us a percentage value how far we are away from factor 1/1000.';
        $content[] = "\n\n";
        $content[] = 'Hit Ratio by SF: %f';

        return sprintf(
            implode(' ', $content),
            $this->getHitRatioBySF($page->getStatusValues()),
        );
    }

    /**
     * get hit ratio of innoDb Buffer by SF
     */
    protected function getHitRatioBySF(StatusValues $status): float
    {
        // we always want a factor of 1/1000.
        $niceToHave = $status['Innodb_buffer_pool_reads'] * 1000;
        $hitRatio = 100 / $niceToHave * $status['Innodb_buffer_pool_read_requests'];
        if ($hitRatio <= 70) {
            $this->setState(StateEnumeration::STATE_ERROR);
        } elseif ($hitRatio <= 90) {
            $this->setState(StateEnumeration::STATE_WARNING);
        } else {
            $this->setState(StateEnumeration::STATE_OK);
        }

        return round($hitRatio, 2);
    }
}

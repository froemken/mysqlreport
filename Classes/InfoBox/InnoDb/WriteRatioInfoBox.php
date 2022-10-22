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
 * InfoBox to inform about InnoDB buffer write ratio
 */
class WriteRatioInfoBox extends AbstractInfoBox
{
    protected $pageIdentifier = 'innoDb';

    protected $title = 'Write Ratio';

    public function renderBody(Page $page): string
    {
        if (!isset($page->getStatusValues()['Innodb_page_size'])) {
            $this->shouldBeRendered = false;
            return '';
        }

        $content = [];
        $content[] = 'As higher than 1 as better.';
        $content[] = "\n\n";
        $content[] = 'Write Ratio: %f';

        return sprintf(
            implode(' ', $content),
            $this->getWriteRatio($page->getStatusValues())
        );
    }

    /**
     * get write ratio of innoDb Buffer
     * A value higher than 1 is good
     */
    protected function getWriteRatio(StatusValues $status): float
    {
        $writeRatio = $status['Innodb_buffer_pool_write_requests'] / $status['Innodb_buffer_pool_pages_flushed'];
        if ($writeRatio <= 2) {
            $this->setState(StateEnumeration::STATE_ERROR);
        } elseif ($writeRatio <= 7) {
            $this->setState(StateEnumeration::STATE_WARNING);
        } else {
            $this->setState(StateEnumeration::STATE_OK);
        }

        return round($writeRatio, 2);
    }
}

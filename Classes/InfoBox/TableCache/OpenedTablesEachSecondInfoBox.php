<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\TableCache;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\Menu\Page;

/**
 * InfoBox to inform about Table Cache Opened table definitions
 */
class OpenedTablesEachSecondInfoBox extends AbstractInfoBox
{
    protected $pageIdentifier = 'tableCache';

    protected $title = 'Opened Tables each second';

    public function renderBody(Page $page): string
    {
        if (!isset($page->getStatusValues()['Opened_table_definitions'])) {
            $this->shouldBeRendered = false;
            return '';
        }

        $content = [];
        $content[] = 'As lower as better';
        $content[] = "\n\n";
        $content[] = 'Opened tables each second: %f';

        return sprintf(
            implode(' ', $content),
            $this->getOpenedTablesEachSecond($page->getStatusValues())
        );
    }

    /**
     * get amount of opened tables each second
     */
    protected function getOpenedTablesEachSecond(StatusValues $status): float
    {
        $openedTables = $status['Opened_tables'] / $status['Uptime'];
        if ($openedTables <= 0.6) {
            $this->setState(StateEnumeration::STATE_OK);
        } elseif ($openedTables <= 4) {
            $this->setState(StateEnumeration::STATE_WARNING);
        } else {
            $this->setState(StateEnumeration::STATE_ERROR);
        }

        return round($openedTables, 4);
    }
}

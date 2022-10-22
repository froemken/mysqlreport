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
class OpenedTableDefinitionsEachSecondInfoBox extends AbstractInfoBox
{
    protected $pageIdentifier = 'tableCache';

    protected $title = 'Opened Table Definitions each second';

    public function renderBody(Page $page): string
    {
        if (!isset($page->getStatusValues()['Opened_table_definitions'])) {
            $this->shouldBeRendered = false;
            return '';
        }

        $content = [];
        $content[] = 'As lower as better';
        $content[] = "\n\n";
        $content[] = 'Opened table definitions each second: %f';

        return sprintf(
            implode(' ', $content),
            $this->getOpenedTableDefinitionsEachSecond($page->getStatusValues())
        );
    }

    /**
     * Get amount of opened table definitions each second
     */
    protected function getOpenedTableDefinitionsEachSecond(StatusValues $status): float
    {
        $openedTableDefinitions = $status['Opened_table_definitions'] / $status['Uptime'];
        if ($openedTableDefinitions <= 0.3) {
            $this->setState(StateEnumeration::STATE_OK);
        } elseif ($openedTableDefinitions <= 2) {
            $this->setState(StateEnumeration::STATE_WARNING);
        } else {
            $this->setState(StateEnumeration::STATE_ERROR);
        }

        return round($openedTableDefinitions, 4);
    }
}

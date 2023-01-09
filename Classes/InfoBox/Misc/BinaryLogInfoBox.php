<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\Misc;

use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\Menu\Page;

/**
 * InfoBox to inform about binary log
 */
class BinaryLogInfoBox extends AbstractInfoBox
{
    protected string $pageIdentifier = 'misc';

    protected string $title = 'Binary Log';

    public function renderBody(Page $page): string
    {
        if (
            isset($page->getStatusValues()['Slave_running'])
            && (
                strtolower($page->getStatusValues()['Slave_running']) === 'off'
                || (int)$page->getStatusValues()['Slave_running'] === 0
            )
        ) {
            $content = [];
            $content[] = 'Your server runs in standalone-mode.';
            $content[] = 'You don\'t need replication. So you can deactivate binary logging';
            $content[] = "\n\n";
            $content[] = 'Link: http://dev.mysql.com/doc/refman/5.6/en/glossary.html#glos_binary_log';
            $content[] = "\n\n";
            $content[] = 'Quote: "The binary logging feature can be turned on and off, although Oracle recommends always';
            $content[] = 'enabling it if you use replication or perform backups."';

            return implode(' ', $content);
        }

        $this->shouldBeRendered = false;

        return '';
    }
}

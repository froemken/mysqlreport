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
 * InfoBox to inform you, if your server runs in standalone or replication mode
 */
class StandaloneReplicationInfoBox extends AbstractInfoBox
{
    protected string $pageIdentifier = 'misc';

    protected string $title = 'Standalone or Replication';

    public function renderBody(Page $page): string
    {
        if (!isset($page->getStatusValues()['Slave_running'])) {
            $this->shouldBeRendered = false;
            return '';
        }

        $content = [];
        $content[] = 'If slave_running is ON, you have a network of master-slave servers.';
        $content[] = 'Else your server runs in standalone-mode';
        $content[] = "\n\n";
        $content[] = 'Slave_running: %s';

        return sprintf(
            implode(' ', $content),
            $page->getStatusValues()['Slave_running']
        );
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\Information;

use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\Menu\Page;

/**
 * InfoBox to inform about uptime and uptime since last flush
 */
class UptimeInfoBox extends AbstractInfoBox
{
    protected string $pageIdentifier = 'information';

    protected string $title = 'Uptime';

    public function renderBody(Page $page): string
    {
        if (isset($page->getStatusValues()['Uptime'])) {
            $this->addUnorderedListEntry(
                $page->getStatusValues()['Uptime'] . ' seconds',
                'Uptime'
            );

            $this->addUnorderedListEntry(
                $this->convertSecondsToDays((int)$page->getStatusValues()['Uptime']) . ' days',
                'Uptime in days'
            );
        }

        if (isset($page->getStatusValues()['Uptime_since_flush_status'])) {
            $this->addUnorderedListEntry(
                $page->getStatusValues()['Uptime_since_flush_status'] . ' seconds',
                'Uptime since last flush'
            );

            $this->addUnorderedListEntry(
                $this->convertSecondsToDays((int)$page->getStatusValues()['Uptime_since_flush_status']) . ' days',
                'Uptime since last flush in days'
            );
        }

        return '"Uptime" shows time since server start or last restart. '
            . 'As a server admin you can FLUSH STATUS to reset various status variables. '
            . 'This is good for temporary debugging, but breaks analysis of server over full time.'
            . 'If "Uptime_since_flush_status" is lower than "Uptime" an admin has reset the status variables.';
    }

    private function convertSecondsToDays(int $seconds): string
    {
        return number_format($seconds / 60 / 60 / 24, 2, ',', '.');
    }
}

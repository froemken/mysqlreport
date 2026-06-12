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
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxUnorderedListInterface;
use StefanFroemken\Mysqlreport\InfoBox\ListElement;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about uptime and uptime since last flush
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.status',
    attributes: ['priority' => 80],
)]
class UptimeInfoBox extends AbstractInfoBox implements InfoBoxUnorderedListInterface
{

    protected const TITLE = 'Uptime';

    public function renderBody(): string
    {
        return '"Uptime" shows the time since the server was started or restarted. '
            . 'Database administrators can execute "FLUSH STATUS" to reset various status variables. '
            . 'While useful for temporary debugging, resetting the status variables interrupts long-term profiling analysis. '
            . 'If "Uptime_since_flush_status" is lower than "Uptime", the status variables have been reset since the server started.';
    }

    private function convertSecondsToDays(int $seconds): string
    {
        return number_format($seconds / 60 / 60 / 24, 2, ',', '.');
    }

    /**
     * @return \SplQueue<ListElement>
     */
    public function getUnorderedList(): \SplQueue
    {
        $unorderedList = new \SplQueue();

        if (isset($this->getStatusValues()['Uptime'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Uptime',
                value: $this->getStatusValues()['Uptime'] . ' seconds',
            ));

            $unorderedList->enqueue(new ListElement(
                title: 'Uptime in days',
                value: $this->convertSecondsToDays((int)$this->getStatusValues()['Uptime']) . ' days',
            ));
        }

        if (isset($this->getStatusValues()['Uptime_since_flush_status'])) {
            $unorderedList->enqueue(new ListElement(
                title: 'Uptime since status variables reset',
                value: $this->getStatusValues()['Uptime_since_flush_status'] . ' seconds',
            ));

            $unorderedList->enqueue(new ListElement(
                title: 'Uptime since status variables reset in days',
                value: $this->convertSecondsToDays((int)$this->getStatusValues()['Uptime_since_flush_status']) . ' days',
            ));
        }

        return $unorderedList;
    }
}

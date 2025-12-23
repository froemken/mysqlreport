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
use StefanFroemken\Mysqlreport\Traits\GetStatusValuesAndVariablesTrait;
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
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Uptime';

    public function renderBody(): string
    {
        return '"Uptime" shows time since server start or last restart. '
            . 'As a server admin you can FLUSH STATUS to reset various status variables. '
            . 'This is good for temporary debugging, but breaks analysis of server over full time.'
            . 'If "Uptime_since_flush_status" is lower than "Uptime" an admin has reset the status variables.';
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
                title: 'Uptime since last flush',
                value: $this->getStatusValues()['Uptime_since_flush_status'] . ' seconds',
            ));

            $unorderedList->enqueue(new ListElement(
                title: 'Uptime since last flush in days',
                value: $this->convertSecondsToDays((int)$this->getStatusValues()['Uptime_since_flush_status']) . ' days',
            ));
        }

        return $unorderedList;
    }
}

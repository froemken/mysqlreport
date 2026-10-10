<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\Information;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxInterface;
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
final readonly class UptimeInfoBox implements InfoBoxInterface, InfoBoxUnorderedListInterface
{
    public function getTitle(): string
    {
        return 'Uptime';
    }

    public function getBody(StatusValues $statusValues, Variables $variables): string
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
     * @return ListElement[]
     */
    public function getUnorderedList(StatusValues $statusValues, Variables $variables): array
    {
        $unorderedList = [];
        $this->addUptimeElements($unorderedList, $statusValues);
        $this->addUptimeSinceFlushElements($unorderedList, $statusValues);

        return $unorderedList;
    }

    /**
     * @param ListElement[] $unorderedList
     */
    private function addUptimeElements(array &$unorderedList, StatusValues $statusValues): void
    {
        if (!isset($statusValues['Uptime'])) {
            return;
        }

        $unorderedList[] = new ListElement(
            title: 'Uptime',
            value: $statusValues['Uptime'] . ' seconds',
        );
        $unorderedList[] = new ListElement(
            title: 'Uptime in days',
            value: $this->convertSecondsToDays((int)$statusValues['Uptime']) . ' days',
        );
    }

    /**
     * @param ListElement[] $unorderedList
     */
    private function addUptimeSinceFlushElements(array &$unorderedList, StatusValues $statusValues): void
    {
        if (!isset($statusValues['Uptime_since_flush_status'])) {
            return;
        }

        $unorderedList[] = new ListElement(
            title: 'Uptime since status variables reset',
            value: $statusValues['Uptime_since_flush_status'] . ' seconds',
        );
        $unorderedList[] = new ListElement(
            title: 'Uptime since status variables reset in days',
            value: $this->convertSecondsToDays((int)$statusValues['Uptime_since_flush_status']) . ' days',
        );
    }
}

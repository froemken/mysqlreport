<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\Information;

use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\Menu\Page;

/**
 * InfoBox to inform about current connections and max used connections
 */
class ConnectionInfoBox extends AbstractInfoBox
{
    protected string $pageIdentifier = 'information';

    protected string $title = 'Connections';

    public function renderBody(Page $page): string
    {
        if (isset($page->getStatusValues()['Connections'])) {
            $this->addUnorderedListEntry($page->getStatusValues()['Connections'], 'Connections in total (Connections)');
            $this->addUnorderedListEntry(
                $this->getAverage(
                    (int)$page->getStatusValues()['Connections'],
                    (int)$page->getStatusValues()['Uptime']
                ),
                'Average connections each second'
            );
        }

        if (isset($page->getStatusValues()['Max_used_connections'])) {
            $this->addUnorderedListEntry(
                $page->getStatusValues()['Max_used_connections'],
                'Max used simultaneous connections (Max_used_connections)'
            );

            if (isset($page->getVariables()['max_connections'])) {
                $percent = 100 / (int)$page->getVariables()['max_connections'] * (int)$page->getStatusValues()['Max_used_connections'];
                if ($percent > 90) {
                    $this->setState(StateEnumeration::STATE_ERROR);
                } elseif ($percent > 70) {
                    $this->setState(StateEnumeration::STATE_WARNING);
                }

                $this->addUnorderedListEntry(
                    number_format($percent, 2, ',', '.') . '%',
                    'Max used simultaneous connections in percent'
                );
            }
        }

        if (isset($page->getVariables()['max_connections'])) {
            $this->addUnorderedListEntry(
                $page->getVariables()['max_connections'],
                'Max allowed simultaneous connections (max_connections)'
            );
        }

        if (isset($page->getVariables()['extra_max_connections'])) {
            $this->addUnorderedListEntry(
                $page->getVariables()['extra_max_connections'],
                'Extra connections (extra_max_connections)'
            );
        }

        if (isset($page->getVariables()['max_user_connections'])) {
            if ((int)$page->getVariables()['max_user_connections'] === 0) {
                $this->addUnorderedListEntry(
                    'No limit. Using value from max_connections',
                    'Max allowed user connections (max_user_connections)'
                );
            } else {
                $this->addUnorderedListEntry(
                    $page->getVariables()['max_user_connections'],
                    'Max allowed user connections (max_user_connections)'
                );
            }
        }

        return 'Have an eye on your servers connections. '
            . 'It is a first indicator how much your server has to do. '
            . 'If the percentage usage of max simultaneous connections is high you should consider '
            . 'to increase the value for max_connections';
    }

    private function getAverage(int $value, int $uptime): string
    {
        return number_format($value / $uptime, 2, ',', '.');
    }
}

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
use StefanFroemken\Mysqlreport\Traits\GetStatusValuesAndVariablesTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform you, if your server runs in standalone or replication mode
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.misc',
)]
class StandaloneReplicationInfoBox extends AbstractInfoBox
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Standalone or Replication';

    public function renderBody(): string
    {
        if (!isset($this->getStatusValues()['Slave_running'])) {
            return '';
        }

        $content = [];
        $content[] = 'If slave_running is ON, you have a network of master-slave servers.';
        $content[] = 'Else your server runs in standalone-mode';
        $content[] = "\n\n";
        $content[] = 'Slave_running: %s';

        return sprintf(
            implode(' ', $content),
            $this->getStatusValues()['Slave_running'],
        );
    }
}

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
 * InfoBox to inform about binary log sync
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.misc',
)]
class SyncBinaryLogInfoBox extends AbstractInfoBox
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Sync Binary Log';

    public function renderBody(): string
    {
        // Sync_binlog does not exist on MariaDB
        if (!isset($this->getStatusValues()['Sync_binlog'])) {
            return '';
        }

        $content = [];
        $content[] = 'By default the binary log is NOT written to disk with each write.';
        $content[] = 'So, if MySQL or the operating system crashes there is a little chance that the last query gets lost.';
        $content[] = 'You can prevent that with enabling sync_binlog, but that slows down your modifying statements like (INSERT, UPDATE, DELETE).';
        $content[] = "\n\n";
        $content[] = 'Status "Sync_binlog": %s';

        return sprintf(
            implode(' ', $content),
            $this->getStatusValues()['Sync_binlog'] ? 'ON' : 'OFF',
        );
    }
}

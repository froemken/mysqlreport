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
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about binary log
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.misc',
)]
final readonly class BinaryLogInfoBox extends AbstractInfoBox
{

    public const TITLE = 'Binary Log';

    public function getBody(): string
    {
        if (
            isset($this->statusValues['Slave_running'])
            && (
                strtolower($this->statusValues['Slave_running']) === 'off'
                || (int)$this->statusValues['Slave_running'] === 0
            )
        ) {
            $content = [];
            $content[] = 'Your server runs in standalone-mode.';
            $content[] = 'You don\'t need replication. So you can deactivate binary logging';
            $content[] = "\n\n";
            $content[] = 'Link: https://dev.mysql.com/doc/refman/8.0/en/glossary.html#glos_binary_log';
            $content[] = "\n\n";
            $content[] = 'Quote: "The binary logging feature can be turned on and off, although Oracle recommends always';
            $content[] = 'enabling it if you use replication or perform backups."';

            return implode(' ', $content);
        }

        return '';
    }
}

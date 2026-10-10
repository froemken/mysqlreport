<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\Misc;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about binary log
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.misc',
)]
final readonly class BinaryLogInfoBox implements InfoBoxInterface
{
    public function getTitle(): string
    {
        return 'Binary Log';
    }

    public function getBody(StatusValues $statusValues, Variables $variables): string
    {
        $slaveRunning = strtolower((string)($statusValues['Slave_running'] ?? ''));
        if (!isset($statusValues['Slave_running']) || ($slaveRunning !== 'off' && (int)$statusValues['Slave_running'] !== 0)) {
            return '';
        }

        $content = [
            'Your server runs in standalone-mode.',
            'You don\'t need replication. So you can deactivate binary logging',
            "\n\n",
            'Link: https://dev.mysql.com/doc/refman/8.0/en/glossary.html#glos_binary_log',
            "\n\n",
            'Quote: "The binary logging feature can be turned on and off, although Oracle recommends always',
            'enabling it if you use replication or perform backups."',
        ];

        return implode(' ', $content);
    }
}

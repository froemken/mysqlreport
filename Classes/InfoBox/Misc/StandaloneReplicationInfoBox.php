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
 * InfoBox to inform you, if your server runs in standalone or replication mode
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.misc',
)]
final readonly class StandaloneReplicationInfoBox implements InfoBoxInterface
{
    public function getTitle(): string
    {
        return 'Standalone or Replication';
    }

    public function getBody(StatusValues $statusValues, Variables $variables): string
    {
        if (!isset($statusValues['Slave_running'])) {
            return '';
        }

        $content = [
            'If slave_running is ON, you have a network of master-slave servers.',
            'Else your server runs in standalone-mode',
            "\n\n",
            'Slave_running: %s',
        ];

        return sprintf(
            implode(' ', $content),
            $statusValues['Slave_running'],
        );
    }
}

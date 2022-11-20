<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\EventListener;

use StefanFroemken\Mysqlreport\Event\ModifyProfileRecordsEvent;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * EventListener to reduce precision of duration column to 6.
 */
class RoundDurationOfProfileRecordsEventListener
{
    public function __invoke(ModifyProfileRecordsEvent $event)
    {
        foreach ($event->getProfileRecords() as $key => $profileRecord) {
            if (!isset($profileRecord['duration'])) {
                continue;
            }

            if (!MathUtility::canBeInterpretedAsFloat($profileRecord['duration'])) {
                continue;
            }

            $profileRecord['duration'] = number_format(
                (float)$profileRecord['duration'],
                6,
                '.',
                ''
            );

            $event->updateProfileRecord($key, $profileRecord);
        }
    }
}

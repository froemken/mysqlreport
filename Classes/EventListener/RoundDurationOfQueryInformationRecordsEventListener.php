<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\EventListener;

use StefanFroemken\Mysqlreport\Event\ModifyQueryInformationRecordsEvent;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * EventListener to reduce precision of duration column to 6.
 */
class RoundDurationOfQueryInformationRecordsEventListener
{
    public function __invoke(ModifyQueryInformationRecordsEvent $event): void
    {
        foreach ($event->getQueryInformationRecords() as $key => $queryInformationRecord) {
            if (!isset($queryInformationRecord['duration'])) {
                continue;
            }

            if (!MathUtility::canBeInterpretedAsFloat($queryInformationRecord['duration'])) {
                continue;
            }

            $queryInformationRecord['duration'] = number_format(
                (float)$queryInformationRecord['duration'],
                6,
                '.',
                '',
            );

            $event->updateQueryInformationRecord($key, $queryInformationRecord);
        }
    }
}

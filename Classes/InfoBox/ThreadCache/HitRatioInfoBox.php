<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\ThreadCache;

use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use StefanFroemken\Mysqlreport\Traits\GetStatusValuesAndVariablesTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about a Thread Cache hit ratio
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.thread_cache',
)]
class HitRatioInfoBox extends AbstractInfoBox implements InfoBoxStateInterface
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Hit Ratio';

    public function renderBody(): string
    {
        if (!isset($this->getVariables()['thread_cache_size'])) {
            return '';
        }

        $content = [];
        $content[] = 'As closer to 100%% as better.';
        $content[] = "\n\n";

        if ((int)$this->getVariables()['thread_cache_size'] === 0) {
            $content[] = 'Your thread_cache_size (0) is not activated. Please set this value 10.';
        } elseif ($this->getStatusValues()['Threads_connected'] < $this->getVariables()['thread_cache_size']) {
            $content[] = 'It seems that you are the only person on this MySQL-Server, so this value should be OK.';
        } else {
            $content[] = 'It seems that your thread_cache_size (' . $this->getVariables()['thread_cache_size'] . ') is too low.';
            $content[] = 'Please increase this value in 10th steps.';
        }

        $content[] = "\n\n";
        $content[] = 'Hit Ratio: %f';

        return sprintf(
            implode(' ', $content),
            $this->getHitRatio(),
        );
    }

    /**
     * get hit ratio of threads cache
     * A ratio nearly 100 would be cool
     */
    protected function getHitRatio(): float
    {
        $status = $this->getStatusValues();

        $hitRatio = 100 - (($status['Threads_created'] / $status['Connections']) * 100);

        return round($hitRatio, 2);
    }

    public function getState(): StateEnumeration
    {
        $hitRatio = $this->getHitRatio();
        if ($hitRatio <= 80) {
            $state = StateEnumeration::STATE_ERROR;
        } elseif ($hitRatio <= 95) {
            $state = StateEnumeration::STATE_WARNING;
        } else {
            $state = StateEnumeration::STATE_OK;
        }

        return $state;
    }
}

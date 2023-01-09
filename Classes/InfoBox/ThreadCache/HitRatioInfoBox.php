<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\ThreadCache;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\Menu\Page;

/**
 * InfoBox to inform about Thread Cache hit ratio
 */
class HitRatioInfoBox extends AbstractInfoBox
{
    protected string $pageIdentifier = 'threadCache';

    protected string $title = 'Hit Ratio';

    public function renderBody(Page $page): string
    {
        if (!isset($page->getVariables()['thread_cache_size'])) {
            $this->shouldBeRendered = false;
            return '';
        }

        $content = [];
        $content[] = 'As closer to 100%% as better.';
        $content[] = "\n\n";

        if ((int)$page->getVariables()['thread_cache_size'] === 0) {
            $content[] = 'Your thread_cache_size (0) is not activated. Please set this value 10.';
        } elseif ($page->getStatusValues()['Threads_connected'] < $page->getVariables()['thread_cache_size']) {
            $content[] = 'It seems that you are the only person on this MySQL-Server, so this value should be OK.';
        } else {
            $content[] = 'It seems that your thread_cache_size (' . $page->getVariables()['thread_cache_size'] . ') is too low.';
            $content[] = 'Please increase this value in 10th steps.';
        }

        $content[] = "\n\n";
        $content[] = 'Hit Ratio: %f';

        return sprintf(
            implode(' ', $content),
            $this->getHitRatio($page->getStatusValues())
        );
    }

    /**
     * get hit ratio of threads cache
     * A ratio nearly 100 would be cool
     */
    protected function getHitRatio(StatusValues $status): float
    {
        $hitRatio = 100 - (($status['Threads_created'] / $status['Connections']) * 100);
        if ($hitRatio <= 80) {
            $this->setState(StateEnumeration::STATE_ERROR);
        } elseif ($hitRatio <= 95) {
            $this->setState(StateEnumeration::STATE_WARNING);
        } else {
            $this->setState(StateEnumeration::STATE_OK);
        }

        return round($hitRatio, 2);
    }
}

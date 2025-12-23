<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\InnoDb;

use StefanFroemken\Mysqlreport\Enumeration\StateEnumeration;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use StefanFroemken\Mysqlreport\Traits\GetStatusValuesAndVariablesTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * InfoBox to inform about InnoDB buffer hit ratio
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.innodb',
    attributes: ['priority' => 50],
)]
class HitRatioInfoBox extends AbstractInfoBox implements InfoBoxStateInterface
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Hit Ratio';

    public function renderBody(): string
    {
        if (
            !isset(
                $this->getStatusValues()['Innodb_page_size'],
                $this->getStatusValues()['Innodb_buffer_pool_read_requests'],
                $this->getStatusValues()['Innodb_buffer_pool_reads'],
            )
        ) {
            return '';
        }

        $content = [];
        $content[] = 'As higher as better.';
        $content[] = 'Everything below 90 is really bad. 99 maybe OK, but is not perfect. Everything above 99.9 is great.';
        $content[] = 'Increase the value of innodb_buffer_pool_size to get better results.';
        $content[] = "\n\n";
        $content[] = 'Hit Ratio: %f';

        return sprintf(
            implode(' ', $content),
            $this->getHitRatio(),
        );
    }

    /**
     * get hit ratio of innoDb Buffer
     * A ratio of 99.9 equals 1/1000
     */
    protected function getHitRatio(): float
    {
        $status = $this->getStatusValues();

        $hitRatio = ($status['Innodb_buffer_pool_read_requests'] / ($status['Innodb_buffer_pool_read_requests'] + $status['Innodb_buffer_pool_reads'])) * 100;

        return round($hitRatio, 2);
    }

    public function getState(): StateEnumeration
    {
        $hitRatio = $this->getHitRatio();
        if ($hitRatio <= 90) {
            $state = StateEnumeration::STATE_ERROR;
        } elseif ($hitRatio <= 99.7) {
            $state = StateEnumeration::STATE_WARNING;
        } else {
            $state = StateEnumeration::STATE_OK;
        }

        return $state;
    }
}

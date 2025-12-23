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
 * InfoBox to inform about InnoDB buffer hit ratio by SF
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.innodb',
    attributes: ['priority' => 40],
)]
class HitRatioBySFInfoBox extends AbstractInfoBox implements InfoBoxStateInterface
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Hit Ratio by SF';

    public function renderBody(): string
    {
        if (
            !isset(
                $this->getStatusValues()['Innodb_page_size'],
                $this->getStatusValues()['Innodb_buffer_pool_reads'],
                $this->getStatusValues()['Innodb_buffer_pool_read_requests'],
            )
        ) {
            return '';
        }

        $content = [];
        $content[] = 'IMO we should have a factor of 1/1000 of speed between reading from HDD and reading from RAM.';
        $content[] = 'So, the amount of reading from HDD * 1000 would be our nice-to-have value for reading from RAM.';
        $content[] = '100 / value of nice-to-have * innodb_buffer_pool_read_requests shows us a percentage value how far we are away from factor 1/1000.';
        $content[] = "\n\n";
        $content[] = 'Hit Ratio by SF: %f';

        return sprintf(
            implode(' ', $content),
            $this->getHitRatioBySF(),
        );
    }

    /**
     * get hit ratio of innoDb Buffer by SF
     */
    protected function getHitRatioBySF(): float
    {
        $status = $this->getStatusValues();

        $niceToHave = $status['Innodb_buffer_pool_reads'] * 1000;
        $hitRatio = 100 / $niceToHave * $status['Innodb_buffer_pool_read_requests'];

        return round($hitRatio, 2);
    }

    public function getState(): StateEnumeration
    {
        $status = $this->getStatusValues();

        // We always want a factor of 1/1000.
        $niceToHave = $status['Innodb_buffer_pool_reads'] * 1000;
        $hitRatio = 100 / $niceToHave * $status['Innodb_buffer_pool_read_requests'];
        if ($hitRatio <= 70) {
            $state = StateEnumeration::STATE_ERROR;
        } elseif ($hitRatio <= 90) {
            $state = StateEnumeration::STATE_WARNING;
        } else {
            $state = StateEnumeration::STATE_OK;
        }

        return $state;
    }
}

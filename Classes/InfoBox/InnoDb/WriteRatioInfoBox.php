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
 * InfoBox to inform about InnoDB buffer write ratio
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.innodb',
)]
class WriteRatioInfoBox extends AbstractInfoBox implements InfoBoxStateInterface
{
    use GetStatusValuesAndVariablesTrait;

    protected const TITLE = 'Write Ratio';

    public function renderBody(): string
    {
        if (
            !isset(
                $this->getStatusValues()['Innodb_page_size'],
                $this->getStatusValues()['Innodb_buffer_pool_write_requests'],
                $this->getStatusValues()['Innodb_buffer_pool_pages_flushed'],
            )
            || (int)$this->getStatusValues()['Innodb_buffer_pool_pages_flushed'] === 0
        ) {
            return '';
        }

        $content = [];
        $content[] = 'As higher than 1 as better.';
        $content[] = "\n\n";
        $content[] = 'Write Ratio: %f';

        return sprintf(
            implode(' ', $content),
            $this->getWriteRatio(),
        );
    }

    /**
     * get write ratio of innoDb Buffer
     * A value higher than 1 is good
     */
    protected function getWriteRatio(): float
    {
        $status = $this->getStatusValues();

        $writeRatio = $status['Innodb_buffer_pool_write_requests'] / $status['Innodb_buffer_pool_pages_flushed'];

        return round($writeRatio, 2);
    }

    public function getState(): StateEnumeration
    {
        $writeRatio = $this->getWriteRatio();
        if ($writeRatio <= 2) {
            $state = StateEnumeration::STATE_ERROR;
        } elseif ($writeRatio <= 7) {
            $state = StateEnumeration::STATE_WARNING;
        } else {
            $state = StateEnumeration::STATE_OK;
        }

        return $state;
    }
}

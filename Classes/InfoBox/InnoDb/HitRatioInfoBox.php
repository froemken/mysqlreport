<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\InfoBox\InnoDb;

use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxInterface;
use StefanFroemken\Mysqlreport\InfoBox\InfoBoxStateInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;

/**
 * InfoBox to inform about InnoDB buffer hit ratio
 */
#[AutoconfigureTag(
    name: 'mysqlreport.infobox.innodb',
    attributes: ['priority' => 50],
)]
final readonly class HitRatioInfoBox implements InfoBoxInterface, InfoBoxStateInterface
{
    public const TITLE = 'Hit Ratio';

    public function getBody(StatusValues $statusValues, Variables $variables): string
    {
        if (
            !isset(
                $statusValues['Innodb_page_size'],
                $statusValues['Innodb_buffer_pool_read_requests'],
                $statusValues['Innodb_buffer_pool_reads'],
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
            $this->getHitRatio($statusValues),
        );
    }

    /**
     * get hit ratio of innoDb Buffer
     * A ratio of 99.9 equals 1/1000
     */
    private function getHitRatio(StatusValues $statusValues): float
    {
        $hitRatio = ($statusValues['Innodb_buffer_pool_read_requests'] / ($statusValues['Innodb_buffer_pool_read_requests'] + $statusValues['Innodb_buffer_pool_reads'])) * 100;

        return round($hitRatio, 2);
    }

    public function getState(StatusValues $statusValues, Variables $variables): ContextualFeedbackSeverity
    {
        $hitRatio = $this->getHitRatio($statusValues);
        if ($hitRatio <= 90) {
            $state = ContextualFeedbackSeverity::ERROR;
        } elseif ($hitRatio <= 99.7) {
            $state = ContextualFeedbackSeverity::WARNING;
        } else {
            $state = ContextualFeedbackSeverity::OK;
        }

        return $state;
    }
}

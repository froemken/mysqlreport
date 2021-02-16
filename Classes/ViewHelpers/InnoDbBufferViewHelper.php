<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\ViewHelpers;

use StefanFroemken\Mysqlreport\Domain\Model\Status;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * VH which adds variables regarding InnoDB to template
 */
class InnoDbBufferViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument(
            'status',
            Status::class,
            'Status of MySQL server',
            true
        );
        $this->registerArgument(
            'variables',
            Variables::class,
            'Variables of MySQL server',
            true
        );
    }

    public function render(): string
    {
        $this->templateVariableContainer->add('hitRatio', $this->getHitRatio($this->arguments['status']));
        $this->templateVariableContainer->add('hitRatioBySF', $this->getHitRatioBySF($this->arguments['status']));
        $this->templateVariableContainer->add('writeRatio', $this->getWriteRatio($this->arguments['status']));
        $this->templateVariableContainer->add('load', $this->getLoad($this->arguments['status']));
        $this->templateVariableContainer->add('logFile', $this->getLogFileSize($this->arguments['status'], $this->arguments['variables']));
        $this->templateVariableContainer->add('instances', $this->getInstances($this->arguments['variables']));
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove('hitRatio');
        $this->templateVariableContainer->remove('hitRatioBySF');
        $this->templateVariableContainer->remove('writeRatio');
        $this->templateVariableContainer->remove('load');
        $this->templateVariableContainer->remove('logFile');
        $this->templateVariableContainer->remove('instances');

        return $content;
    }

    /**
     * get hit ratio of innoDb Buffer
     * A ratio of 99.9 equals 1/1000
     *
     * @param Status $status
     * @return array
     */
    protected function getHitRatio(Status $status): array
    {
        $result = [];
        $hitRatio = ($status->getInnodbBufferPoolReadRequests() / ($status->getInnodbBufferPoolReadRequests() + $status->getInnodbBufferPoolReads())) * 100;
        if ($hitRatio <= 90) {
            $result['status'] = 'danger';
        } elseif ($hitRatio <= 99.7) {
            $result['status'] = 'warning';
        } else {
            $result['status'] = 'success';
        }
        $result['value'] = round($hitRatio, 2);

        return $result;
    }

    /**
     * get hit ratio of innoDb Buffer by SF
     *
     * @param Status $status
     * @return array
     */
    protected function getHitRatioBySF(Status $status): array
    {
        $result = [];

        // we always want a factor of 1/1000.
        $niceToHave = $status->getInnodbBufferPoolReads() * 1000;
        $hitRatio = 100 / $niceToHave * $status->getInnodbBufferPoolReadRequests();
        if ($hitRatio <= 70) {
            $result['status'] = 'danger';
        } elseif ($hitRatio <= 90) {
            $result['status'] = 'warning';
        } else {
            $result['status'] = 'success';
        }
        $result['value'] = round($hitRatio, 2);

        return $result;
    }

    /**
     * get write ratio of innoDb Buffer
     * A value more higher than 1 is good
     *
     * @param Status $status
     * @return array
     */
    protected function getWriteRatio(Status $status): array
    {
        $result = [];
        $writeRatio = $status->getInnodbBufferPoolWriteRequests() / $status->getInnodbBufferPoolPagesFlushed();
        if ($writeRatio <= 2) {
            $result['status'] = 'danger';
        } elseif ($writeRatio <= 7) {
            $result['status'] = 'warning';
        } else {
            $result['status'] = 'success';
        }
        $result['value'] = round($writeRatio, 2);

        return $result;
    }

    /**
     * get load of InnoDB Buffer
     *
     * @param Status $status
     * @return array
     */
    protected function getLoad(Status $status): array
    {
        $load = [];

        // in Bytes
        $total = $status->getInnodbBufferPoolPagesTotal() * $status->getInnodbPageSize();
        $data = $status->getInnodbBufferPoolPagesData() * $status->getInnodbPageSize();
        $misc = $status->getInnodbBufferPoolPagesMisc() * $status->getInnodbPageSize();
        $free = $status->getInnodbBufferPoolPagesFree() * $status->getInnodbPageSize();

        // in MB
        $load['total'] = GeneralUtility::formatSize($total);
        $load['data'] = GeneralUtility::formatSize($data);
        $load['misc'] = GeneralUtility::formatSize($misc);
        $load['free'] = GeneralUtility::formatSize($free);

        // in percent
        $load['dataPercent'] = round(100 / $total * $data, 1);
        $load['miscPercent'] = round(100 / $total * $misc, 1);
        $load['freePercent'] = round(100 / $total * $free, 1);

        return $load;
    }

    /**
     * find a good size for log files
     *
     * @link http://www.psce.com/blog/2012/04/10/what-is-the-proper-size-of-innodb-logs/
     *
     * @param Status $status
     * @param Variables $variables
     * @return array
     */
    protected function getLogFileSize(Status $status, Variables $variables): array
    {
        $result = [];

        $bytesWrittenEachSecond = $status->getInnodbOsLogWritten() / $status->getUptime();
        $bytesWrittenEachHour = $bytesWrittenEachSecond * 60 * 60;
        $sizeOfEachLogFile = (int)($bytesWrittenEachHour / $variables->getInnodbLogFilesInGroup());

        if ($sizeOfEachLogFile < 5242880 || $sizeOfEachLogFile < $variables->getInnodbLogFileSize()) {
            $result['status'] = 'success';
        } else {
            $result['status'] = 'danger';
        }
        $result['value'] = $variables->getInnodbLogFileSize();
        $result['niceToHave'] = $sizeOfEachLogFile;

        return $result;
    }

    protected function getInstances(Variables $variables): array
    {
        $result = [];
        $innodbBufferShouldBe = $variables->getInnodbBufferPoolInstances() * (1 * 1024 * 1024 * 1024); // Instances * 1 GB
        if ($variables->getInnodbBufferPoolSize() < (1 * 1024 * 1024 * 1024) && $variables->getInnodbBufferPoolInstances() === 1) {
            $result['status'] = 'success';
        } elseif ($innodbBufferShouldBe !== $variables->getInnodbBufferPoolSize()) {
            $result['status'] = 'danger';
        } else {
            $result['status'] = 'success';
        }
        $result['value'] = $variables->getInnodbBufferPoolInstances();

        return $result;
    }
}

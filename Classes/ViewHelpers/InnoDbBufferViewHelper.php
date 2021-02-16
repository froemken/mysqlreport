<?php
namespace StefanFroemken\Mysqlreport\ViewHelpers;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use StefanFroemken\Mysqlreport\Domain\Model\Status;
use StefanFroemken\Mysqlreport\Domain\Model\Variables;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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

    /**
     * analyze QueryCache parameters
     *
     * @param Status $status
     * @param Variables $variables
     * @return string
     */
    public function render(Status $status, Variables $variables)
    {
        $this->templateVariableContainer->add('hitRatio', $this->getHitRatio($status));
        $this->templateVariableContainer->add('hitRatioBySF', $this->getHitRatioBySF($status));
        $this->templateVariableContainer->add('writeRatio', $this->getWriteRatio($status));
        $this->templateVariableContainer->add('load', $this->getLoad($status));
        $this->templateVariableContainer->add('logFile', $this->getLogFileSize($status, $variables));
        $this->templateVariableContainer->add('instances', $this->getInstances($variables));
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
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Status $status
     * @return array
     */
    protected function getHitRatio(Status $status)
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
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Status $status
     * @return array
     */
    protected function getHitRatioBySF(Status $status)
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
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Status $status
     * @return array
     */
    protected function getWriteRatio(Status $status)
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
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Status $status
     * @return array
     */
    protected function getLoad(Status $status)
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
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Status $status
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Variables $variables
     * @return array
     */
    protected function getLogFileSize(Status $status, Variables $variables)
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

    /**
     * check if instances are set correct
     *
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Variables $variables
     * @return array
     */
    protected function getInstances(Variables $variables)
    {
        $result = [];
        $innodbBufferShouldBe = $variables->getInnodbBufferPoolInstances() * (1 * 1024 * 1024 * 1024); // Instances * 1 GB
        if ($variables->getInnodbBufferPoolSize() < (1 * 1024 * 1024 * 1024) && $variables->getInnodbBufferPoolInstances() === 1) {
            $result['status'] = 'success';
        }	elseif ($innodbBufferShouldBe !== $variables->getInnodbBufferPoolSize()) {
                $result['status'] = 'danger';
        } else {
            $result['status'] = 'success';
        }
        $result['value'] = $variables->getInnodbBufferPoolInstances();
        return $result;
    }
}

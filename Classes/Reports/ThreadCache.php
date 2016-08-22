<?php
namespace StefanFroemken\Mysqlreport\Reports;
    
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
    
/**
 * Analyse key_buffer
 */
class ThreadCache extends AbstractReport
{
    protected $title = 'ThreadCache';

    /**
     * return report to MySqlReport class
     *
     * @return \StefanFroemken\Mysqlreport\Domain\Model\Report
     */
    public function getReport()
    {
        /** @var \StefanFroemken\Mysqlreport\Domain\Model\Report $report */
        $report = $this->objectManager->get('StefanFroemken\\Mysqlreport\\Domain\\Model\\Report');
        $report->setTitle($this->title);
        $this->addImportantVariables($report);
        $this->addImportantStatus($report);

        // add calculation
        $createdThreadsPerSecond = round( $this->status->getThreadsCreated() / $this->status->getUptime(), 4 );
        $this->addCalculation(
            $report,
            'createdThreadsEachSecond.title',
            $createdThreadsPerSecond,
            'createdThreadsEachSecond.description',
            0, 10
        );
        $createdBytes = 256 * 1024 * $this->status->getThreadsCreated();
        $this->addCalculation(
            $report,
            'sumOfAllThreadsInBytes.title',
            $this->formatSize($createdBytes),
            'sumOfAllThreadsInBytes.description',
            0, (10 * 1024 * 1024 * 1024)
        );

        return $report;
    }

    /**
     * add important variables
     *
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Report $report
     * @return void
     */
    protected function addImportantVariables(\StefanFroemken\Mysqlreport\Domain\Model\Report $report)
    {
        $report->addVariable('thread_cache_size', $this->variables->getThreadCacheSize());
    }

    /**
     * add important status
     *
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Report $report
     * @return void
     */
    protected function addImportantStatus(\StefanFroemken\Mysqlreport\Domain\Model\Report $report)
    {
        $report->addStatus('Threads_connected', $this->status->getThreadsConnected());
        $report->addStatus('Threads_created', $this->status->getThreadsCreated());
    }
}

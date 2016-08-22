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
class KeyBuffer extends AbstractReport
{
    protected $title = 'KeyBuffer';

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
        $cacheHitRate = 100 - ( ( $this->status->getKeyReads() * 100 ) / $this->status->getKeyReadRequests() );
        $this->addCalculation(
            $report,
            'cacheHitRatioBook.title',
            $cacheHitRate,
            'cacheHitRatioBook.description',
            95, 100
        );
        $cacheHitRate = ( $this->status->getKeyReads() / $this->status->getKeyReadRequests() );
        $this->addCalculation(
            $report,
            'cacheHitRatioMySql.title',
            $cacheHitRate,
            'cacheHitRatioMySql.description',
            0, 0.01
        );
        $cacheHitRate = ( $this->status->getKeyReadRequests() / $this->status->getKeyReads() );
        $this->addCalculation(
            $report,
            'cacheHitRatio.title',
            $cacheHitRate,
            'cacheHitRatio.description',
            100, 1000000
        );
        $cacheMisses = ( $this->status->getKeyReads() / $this->status->getUptime() );
        $this->addCalculation(
            $report,
            'cacheMisses.title',
            $cacheMisses,
            'cacheMisses.description',
            0, 15
        );
        $usedBuffer = 100 - ( ( $this->status->getKeyBlocksUnused() * $this->variables->getKeyCacheBlockSize() ) * 100 / $this->variables->getKeyBufferSize() );
        $this->addCalculation(
            $report,
            'usedBuffer.title',
            $usedBuffer,
            'usedBuffer.description',
            0, 90
        );
        $indexSize = $this->tableInformationRepository->getIndexSize(\StefanFroemken\Mysqlreport\Domain\Repository\TableInformationRepository::MYISAM);
        $this->addCalculation(
            $report,
            'myIsamIndexSize.title',
            $this->formatSize($indexSize),
            'myIsamIndexSize.description',
            0, ($this->variables->getKeyBufferSize() - ($this->variables->getKeyBufferSize() / 100 * 10))
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
        $report->addVariable('key_buffer_size', $this->formatSize($this->variables->getKeyBufferSize()));
        $report->addVariable('key_cache_block_size', $this->formatSize($this->variables->getKeyCacheBlockSize()));
    }

    /**
     * add important status
     *
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Report $report
     * @return void
     */
    protected function addImportantStatus(\StefanFroemken\Mysqlreport\Domain\Model\Report $report)
    {
        $report->addStatus('Key_blocks_unused', $this->status->getKeyBlocksUnused());
        $report->addStatus('Key_Read_Requests', $this->status->getKeyReadRequests());
        $report->addStatus('Key_Reads', $this->status->getKeyReads());
    }
}

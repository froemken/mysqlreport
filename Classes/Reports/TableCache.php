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
class TableCache extends AbstractReport {

    protected $title = 'TableCache';

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
        $report->setDescription('tableCache');
        $this->addImportantVariables($report);
        $this->addImportantStatus($report);

        // add calculations
        $diffTableDefinitions = $this->variables->getTableDefinitionCache() - $this->status->getOpenTableDefinitions();
        $this->addCalculation(
            $report,
            'freeTableDefinitions.title',
            $diffTableDefinitions,
            'freeTableDefinitions.description',
            ($this->variables->getTableDefinitionCache() / 100 * 10), $this->variables->getTableDefinitionCache()
        );

        $diffTable = $this->variables->getTableOpenCache() - $this->status->getOpenTables();
        $this->addCalculation(
            $report,
            'freeOpenTables.title',
            $diffTable,
            'freeOpenTables.description',
            ($this->variables->getTableOpenCache() / 100 * 10), $this->variables->getTableOpenCache()
        );

        $openedDefsEachSec = $this->status->getOpenedTableDefinitions() / $this->status->getUptime();
        $this->addCalculation(
            $report,
            'openedDefinitionsEachSecond.title',
            $openedDefsEachSec,
            'openedDefinitionsEachSecond.description',
            0, 3
        );

        $openedTablesEachSec = $this->status->getOpenedTables() / $this->status->getUptime();
        $this->addCalculation(
            $report,
            'openedTablesEachSecond.title',
            $openedTablesEachSec,
            'openedTablesEachSecond.description',
            0, 3
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
        $report->addVariable('table_definition_cache', $this->variables->getTableDefinitionCache());
        $report->addVariable('table_open_cache', $this->variables->getTableOpenCache());
    }

    /**
     * add important status
     *
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Report $report
     * @return void
     */
    protected function addImportantStatus(\StefanFroemken\Mysqlreport\Domain\Model\Report $report)
    {
        $report->addStatus('Open_table_definitions', $this->status->getOpenTableDefinitions());
        $report->addStatus('Opened_table_definitions', $this->status->getOpenedTableDefinitions());
        $report->addStatus('Open_tables', $this->status->getOpenTables());
        $report->addStatus('Opened_tables', $this->status->getOpenedTables());
    }
}

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
class SortBuffer extends AbstractReport
{
    protected $title = 'SortBuffer';

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
        $diffTableDefinitions = $this->variables->getTableDefinitionCache() - $this->status->getOpenTableDefinitions();
        $this->addCalculation($report, 'Diff open table definitions', $diffTableDefinitions, 'Persönlich: Alles was größer ist als 10 ist völlig in Ordnung.');
        $diffTable = $this->variables->getTableOpenCache() - $this->status->getOpenTables();
        $this->addCalculation($report, 'Diff open tables', $diffTable, 'Persönlich: Alles was größer ist als 10 ist völlig in Ordnung.');
        $openedDefsEachSec = $this->status->getOpenedTableDefinitions() / $this->status->getUptime();
        $this->addCalculation($report, 'Geöffnete Tabellen Definitionen pro Sekunde', $openedDefsEachSec, '0-3 ist OK. Alles über 10 benötigt Handlungsbedarf.');
        $openedTablesEachSec = $this->status->getOpenedTables() / $this->status->getUptime();
        $this->addCalculation($report, 'Geöffnete Tabellen pro Sekunde', $openedTablesEachSec, '0-3 ist OK. Alles über 10 benötigt Handlungsbedarf.');

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

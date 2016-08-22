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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Analyse key_buffer
 */
abstract class AbstractReport implements ReportInterface
{
    /**
     * @var \StefanFroemken\Mysqlreport\Domain\Repository\StatusRepository
     * @inject
     */
    protected $statusRepository;

    /**
     * @var \StefanFroemken\Mysqlreport\Domain\Repository\VariablesRepository
     * @inject
     */
    protected $variablesRepository;

    /**
     * @var \StefanFroemken\Mysqlreport\Domain\Repository\TableInformationRepository
     * @inject
     */
    protected $tableInformationRepository;

    /**
     * @var \StefanFroemken\Mysqlreport\Domain\Model\Status
     */
    protected $status;

    /**
     * @var \StefanFroemken\Mysqlreport\Domain\Model\Variables
     */
    protected $variables;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     * @inject
     */
    protected $objectManager;

    /**
     * initializes this object
     * fill status and variables
     */
    public function initializeObject()
    {
        $this->variables = $this->variablesRepository->findAll();
        $this->status = $this->statusRepository->findAll();
    }

    /**
     * add calculation
     *
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Report $report
     * @param string $title
     * @param string $result
     * @param string $description
     * @param int $minAllowedValue
     * @param int $maxAllowedValue
     *
     * @return void
     */
    protected function addCalculation(\StefanFroemken\Mysqlreport\Domain\Model\Report $report, $title, $result, $description, $minAllowedValue, $maxAllowedValue)
    {
        /** @var \StefanFroemken\Mysqlreport\Domain\Model\Calculation $calculation */
        $calculation = $this->objectManager->get('StefanFroemken\\Mysqlreport\\Domain\\Model\\Calculation');
        $calculation->setTitle($title);
        $calculation->setDescription($description);
        $calculation->setMinAllowedValue($minAllowedValue);
        $calculation->setMaxAllowedValue($maxAllowedValue);
        $calculation->setResult($result);
        $report->addCalculation($calculation);
    }

    /**
     * format bytes
     *
     * @param $sizeInByte
     * @return string
     */
    public function formatSize($sizeInByte)
    {
        return GeneralUtility::formatSize($sizeInByte, $labels = ' Byte | KB | MB | GB');
    }
}

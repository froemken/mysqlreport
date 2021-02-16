<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Model;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * This model saves the mysql status
 */
class Report
{
    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * description
     *
     * @var string
     */
    protected $description = '';

    /**
     * status
     *
     * @var array
     */
    protected $status = [];

    /**
     * variables
     *
     * @var array
     */
    protected $variables = [];

    /**
     * calculations
     *
     * @var array
     */
    protected $calculations = [];

    /**
     * Getter for title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Setter for title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Getter for description
     *
     * @return string
     */
    public function getDescription()
    {
        $description = LocalizationUtility::translate($this->description, 'mysqlreport');
        if (empty($description)) {
            $description = $this->description;
        }
        return $description;
    }

    /**
     * Setter for description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Getter for status
     *
     * @return array
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Setter for status
     *
     * @param array $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Adds a status
     *
     * @param string $key
     * @param string $value
     */
    public function addStatus($key, $value)
    {
        $this->status[$key] = $value;
    }

    /**
     * Getter for variables
     *
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Setter for variables
     *
     * @param array $variables
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

    /**
     * Adds a variable
     *
     * @param string $key
     * @param string $value
     */
    public function addVariable($key, $value)
    {
        $this->variables[$key] = $value;
    }

    /**
     * Getter for calculations
     *
     * @return array
     */
    public function getCalculations()
    {
        return $this->calculations;
    }

    /**
     * Setter for calculations
     *
     * @param array $calculations
     */
    public function setCalculations($calculations)
    {
        $this->calculations = $calculations;
    }

    /**
     * Adds a calculation
     *
     * @param \StefanFroemken\Mysqlreport\Domain\Model\Calculation $calculation
     */
    public function addCalculation(\StefanFroemken\Mysqlreport\Domain\Model\Calculation $calculation)
    {
        $this->calculations[] = $calculation;
    }
}

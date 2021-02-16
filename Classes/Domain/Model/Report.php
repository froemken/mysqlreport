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
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var array
     */
    protected $status = [];

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @var array
     */
    protected $calculations = [];

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        $description = LocalizationUtility::translate($this->description, 'mysqlreport');
        if (empty($description)) {
            $description = $this->description;
        }
        return $description;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function getStatus(): array
    {
        return $this->status;
    }

    public function setStatus(array $status)
    {
        $this->status = $status;
    }

    public function addStatus(string $key, string $value)
    {
        $this->status[$key] = $value;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setVariables(array $variables)
    {
        $this->variables = $variables;
    }

    public function addVariable(string $key, string $value)
    {
        $this->variables[$key] = $value;
    }

    /**
     * @return array|Calculation[]
     */
    public function getCalculations(): array
    {
        return $this->calculations;
    }

    /**
     * @param array|Calculation[] $calculations
     */
    public function setCalculations(array $calculations)
    {
        $this->calculations = $calculations;
    }

    public function addCalculation(Calculation $calculation)
    {
        $this->calculations[] = $calculation;
    }
}

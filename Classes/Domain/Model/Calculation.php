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
 * Calculation
 */
class Calculation
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
     * result
     *
     * @var string
     */
    protected $result = '';

    /**
     * minAllowedValue
     *
     * @var int
     */
    protected $minAllowedValue = 0;

    /**
     * maxAllowedValue
     *
     * @var int
     */
    protected $maxAllowedValue = 0;

    /**
     * Getter for title
     *
     * @return string
     */
    public function getTitle()
    {
        $title = LocalizationUtility::translate($this->title, 'mysqlreport');
        if (empty($title)) {
            $title = $this->title;
        }
        return $title;
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
     * Getter for result
     *
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Setter for result
     *
     * @param string $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * Getter for minAllowedValue
     *
     * @return int
     */
    public function getMinAllowedValue()
    {
        return $this->minAllowedValue;
    }

    /**
     * Setter for minAllowedValue
     *
     * @param int $minAllowedValue
     */
    public function setMinAllowedValue($minAllowedValue)
    {
        $this->minAllowedValue = $minAllowedValue;
    }

    /**
     * Getter for maxAllowedValue
     *
     * @return int
     */
    public function getMaxAllowedValue()
    {
        return $this->maxAllowedValue;
    }

    /**
     * Setter for maxAllowedValue
     *
     * @param int $maxAllowedValue
     */
    public function setMaxAllowedValue($maxAllowedValue)
    {
        $this->maxAllowedValue = $maxAllowedValue;
    }

    /**
     * is value in range
     *
     * @return bool
     */
    public function isInRange()
    {
        if ($this->result >= $this->minAllowedValue && $this->result <= $this->maxAllowedValue) {
            // everything is OK
            return true;
        } else {
            // value too high or low
            return false;
        }
    }

}

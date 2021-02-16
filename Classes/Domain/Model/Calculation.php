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
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $result = '';

    /**
     * @var int
     */
    protected $minAllowedValue = 0;

    /**
     * @var int
     */
    protected $maxAllowedValue = 0;

    public function getTitle(): string
    {
        $title = LocalizationUtility::translate($this->title, 'mysqlreport');
        if (empty($title)) {
            $title = $this->title;
        }
        return $title;
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

    public function getResult(): string
    {
        return $this->result;
    }

    public function setResult(string $result)
    {
        $this->result = $result;
    }

    public function getMinAllowedValue(): int
    {
        return $this->minAllowedValue;
    }

    public function setMinAllowedValue(int $minAllowedValue)
    {
        $this->minAllowedValue = $minAllowedValue;
    }

    public function getMaxAllowedValue(): int
    {
        return $this->maxAllowedValue;
    }

    public function setMaxAllowedValue(int $maxAllowedValue)
    {
        $this->maxAllowedValue = $maxAllowedValue;
    }

    public function isInRange(): bool
    {
        if ($this->result >= $this->minAllowedValue && $this->result <= $this->maxAllowedValue) {
            // everything is OK
            return true;
        }

        // value too high or low
        return false;
    }
}

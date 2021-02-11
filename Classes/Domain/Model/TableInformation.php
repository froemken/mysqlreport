<?php
namespace StefanFroemken\Mysqlreport\Domain\Model;
    
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
 * This model saves information of a table
 */
class TableInformation
{
    /**
     * tableName
     *
     * @var string
     */
    protected $tableName = '';

    /**
     * engine
     *
     * @var string
     */
    protected $engine = '';

    /**
     * tableRows
     *
     * @var int
     */
    protected $tableRows = 0;

    /**
     * avgRowLength
     *
     * @var int
     */
    protected $avgRowLength = 0;

    /**
     * dataLength
     *
     * @var int
     */
    protected $dataLength = 0;

    /**
     * indexLength
     *
     * @var int
     */
    protected $indexLength = 0;

    /**
     * dataFree
     *
     * @var int
     */
    protected $dataFree = 0;

    /**
     * autoIncrement
     *
     * @var int
     */
    protected $autoIncrement = 0;

    /**
     * Getter for tableName
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Setter for tableName
     *
     * @param string $tableName
     * @return void
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Getter for engine
     *
     * @return string
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * Setter for engine
     *
     * @param string $engine
     * @return void
     */
    public function setEngine($engine)
    {
        $this->engine = $engine;
    }

    /**
     * Getter for tableRows
     *
     * @return int
     */
    public function getTableRows()
    {
        return $this->tableRows;
    }

    /**
     * Setter for tableRows
     *
     * @param int $tableRows
     * @return void
     */
    public function setTableRows($tableRows)
    {
        $this->tableRows = $tableRows;
    }

    /**
     * Getter for avgRowLength
     *
     * @return int
     */
    public function getAvgRowLength()
    {
        return $this->avgRowLength;
    }

    /**
     * Setter for avgRowLength
     *
     * @param int $avgRowLength
     * @return void
     */
    public function setAvgRowLength($avgRowLength)
    {
        $this->avgRowLength = $avgRowLength;
    }

    /**
     * Getter for dataLength
     *
     * @return int
     */
    public function getDataLength()
    {
        return $this->dataLength;
    }

    /**
     * Setter for dataLength
     *
     * @param int $dataLength
     * @return void
     */
    public function setDataLength($dataLength)
    {
        $this->dataLength = $dataLength;
    }

    /**
     * Getter for indexLength
     *
     * @return int
     */
    public function getIndexLength()
    {
        return $this->indexLength;
    }

    /**
     * Setter for indexLength
     *
     * @param int $indexLength
     * @return void
     */
    public function setIndexLength($indexLength)
    {
        $this->indexLength = $indexLength;
    }

    /**
     * Getter for dataFree
     *
     * @return int
     */
    public function getDataFree()
    {
        return $this->dataFree;
    }

    /**
     * Setter for dataFree
     *
     * @param int $dataFree
     * @return void
     */
    public function setDataFree($dataFree)
    {
        $this->dataFree = $dataFree;
    }

    /**
     * Getter for autoIncrement
     *
     * @return int
     */
    public function getAutoIncrement()
    {
        return $this->autoIncrement;
    }

    /**
     * Setter for autoIncrement
     *
     * @param int $autoIncrement
     * @return void
     */
    public function setAutoIncrement($autoIncrement)
    {
        $this->autoIncrement = $autoIncrement;
    }

}

<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Analysis;

/**
 * Interface for Analysis classes
 */
interface AnalysisInterface
{
    /**
     * Returns an identifier for this Analysis
     * Helpful to differ analysis objects for better rendering in Fluid template
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Returns a title for this Analysis
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Returns a group name.
     * Useful to collect all analysis by group like "InnoDB" or "QueryCache"
     *
     * @return string
     */
    public function getGroup(): string;

    /**
     * Returns a description to explain this formula.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Returns a recommendation to improve performance
     *
     * @return string
     */
    public function getRecommendation(): string;

    /**
     * Returns visual representation of the calculation.
     * It does not calculate anything.
     *
     * @return string
     */
    public function getFormula(): string;

    /**
     * Returns one of the Bootstrap classes like success, warning, error, alert and info
     * to represent a colorful status.
     *
     * @return string
     */
    public function getCssClass(): string;

    /**
     * Returns the plain value of the calculation (int/float).
     * Useful to choose the correct CSS class
     *
     * if ($this->getPlainValue() > 99.9)...
     *
     * This would not be possible with getResult()
     *
     * @return mixed
     */
    public function getPlainResult();

    /**
     * Returns the formatted result of the calculation.
     * Like 23.45%
     *
     * @return string
     */
    public function getResult(): string;
}

<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Configuration;

use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Model with properties for InfoBoxes you can see in BE module
 */
class InfoBoxConfiguration
{
    /**
     * @var string
     */
    private $class = '';

    /**
     * @var string
     */
    private $pageIdentifier = '';

    public function __construct(array $configuration)
    {
        if ($this->isValid($configuration)) {
            $this->class = $configuration['class'];
            $this->pageIdentifier = $configuration['pageIdentifier'];
        } else {
            throw new \UnexpectedValueException('Invalid configuration for InfoBoxConfiguration', 1666533406);
        }
    }

    private function isValid(array $configuration): bool
    {
        if (
            !isset(
                $configuration['class'],
                $configuration['pageIdentifier'],
            )
        ) {
            return false;
        }

        if (
            !is_string($configuration['class'])
            || !is_string($configuration['pageIdentifier'])
        ) {
            return false;
        }

        if (
            $configuration['class'] === ''
            || $configuration['pageIdentifier'] === ''
        ) {
            return false;
        }

        if (!class_exists($configuration['class'])) {
            return false;
        }

        return is_subclass_of($configuration['class'], AbstractInfoBox::class);
    }

    public function getPageIdentifier(): string
    {
        return $this->pageIdentifier;
    }

    public function getInfoBox(): AbstractInfoBox
    {
        return GeneralUtility::makeInstance($this->class);
    }
}

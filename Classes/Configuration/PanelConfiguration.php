<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Configuration;

use StefanFroemken\Mysqlreport\Panel\AbstractPanel;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Model with properties for panels you can see in BE module
 */
class PanelConfiguration
{
    /**
     * @var string
     */
    protected $class = '';

    /**
     * @var string
     */
    protected $pageIdentifier = '';

    /**
     * @var array
     */
    protected $configuration = [];

    public function __construct(array $configuration)
    {
        $this->class = $configuration['class'] ?? '';
        $this->pageIdentifier = $configuration['pageIdentifier'] ?? '';

        $this->configuration = $configuration;
    }

    public function isValid(): bool
    {
        if ($this->configuration['class'] === '') {
            return false;
        }

        if ($this->configuration['pageIdentifier'] === '') {
            return false;
        }

        return class_exists($this->configuration['class']);
    }

    public function getPageIdentifier(): string
    {
        return $this->isValid() ? $this->pageIdentifier : '';
    }

    public function getPanel(): ?AbstractPanel
    {
        return $this->isValid() ? GeneralUtility::makeInstance($this->class) : null;
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Configuration;

use StefanFroemken\Mysqlreport\Traits\Typo3RequestTrait;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * This class will streamline the values from extension settings
 */
class ExtConf
{
    use Typo3RequestTrait;

    private bool $enableFrontendLogging = false;

    private bool $enableBackendLogging = false;

    private bool $activateExplainQuery = false;

    private float $slowQueryThreshold = 10.0;

    public function __construct(ExtensionConfiguration $extensionConfiguration)
    {
        try {
            $extConf = (array)$extensionConfiguration->get('mysqlreport');
        } catch (ExtensionConfigurationExtensionNotConfiguredException | ExtensionConfigurationPathDoesNotExistException $exception) {
            // Use default values
            return;
        }

        if ($extConf === []) {
            return;
        }

        // call setter method foreach configuration entry
        foreach ($extConf as $key => $value) {
            $methodName = 'set' . ucfirst($key);
            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            }
        }
    }

    public function isEnableFrontendLogging(): bool
    {
        return $this->enableFrontendLogging;
    }

    public function setEnableFrontendLogging(string $enableFrontendLogging): void
    {
        $this->enableFrontendLogging = (bool)$enableFrontendLogging;
    }

    public function isEnableBackendLogging(): bool
    {
        return $this->enableBackendLogging;
    }

    public function setEnableBackendLogging(string $enableBackendLogging): void
    {
        $this->enableBackendLogging = (bool)$enableBackendLogging;
    }

    public function isActivateExplainQuery(): bool
    {
        return $this->activateExplainQuery;
    }

    public function setActivateExplainQuery(string $activateExplainQuery): void
    {
        $this->activateExplainQuery = (bool)$activateExplainQuery;
    }

    public function getSlowQueryThreshold(): float
    {
        return $this->slowQueryThreshold;
    }

    public function setSlowQueryThreshold(string $slowQueryThreshold): void
    {
        if (MathUtility::canBeInterpretedAsFloat($slowQueryThreshold)) {
            $this->slowQueryThreshold = (float)$slowQueryThreshold;
        } else {
            $slowQueryThreshold = str_replace(',', '.', $slowQueryThreshold);
            if (MathUtility::canBeInterpretedAsFloat($slowQueryThreshold)) {
                $this->slowQueryThreshold = (float)$slowQueryThreshold;
            }
        }
    }

    public function isQueryLoggingActivated(): bool
    {
        if (Environment::isCli()) {
            return false;
        }

        if ($this->isEnableFrontendLogging() && !$this->isBackendRequest()) {
            return true;
        }

        if ($this->isEnableBackendLogging() && $this->isBackendRequest()) {
            return true;
        }

        return false;
    }
}

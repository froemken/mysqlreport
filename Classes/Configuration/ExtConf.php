<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Configuration;

use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * This class will streamline the values from extension manager configuration
 */
class ExtConf implements SingletonInterface
{
    /**
     * @var bool
     */
    protected $profileFrontend = false;

    /**
     * @var bool
     */
    protected $profileBackend = false;

    /**
     * @var bool
     */
    protected $addExplain = false;

    /**
     * @var float
     */
    protected $slowQueryTime = 10.0;

    public function __construct(ExtensionConfiguration $extensionConfiguration)
    {
        try {
            $extConf = (array)$extensionConfiguration->get('mysqlreport');
        } catch (ExtensionConfigurationExtensionNotConfiguredException | ExtensionConfigurationPathDoesNotExistException $exception) {
            // Use default values
            return;
        }

        if (!is_array($extConf)) {
            return;
        }

        if (empty($extConf)) {
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

    public function isProfileFrontend(): bool
    {
        return $this->profileFrontend;
    }

    public function setProfileFrontend(string $profileFrontend): void
    {
        $this->profileFrontend = (bool)$profileFrontend;
    }

    public function isProfileBackend(): bool
    {
        return $this->profileBackend;
    }

    public function setProfileBackend(string $profileBackend): void
    {
        $this->profileBackend = (bool)$profileBackend;
    }

    public function isAddExplain(): bool
    {
        return $this->addExplain;
    }

    public function setAddExplain(string $addExplain): void
    {
        $this->addExplain = (bool)$addExplain;
    }

    public function getSlowQueryTime(): float
    {
        return $this->slowQueryTime;
    }

    public function setSlowQueryTime(string $slowQueryTime): void
    {
        if (MathUtility::canBeInterpretedAsFloat($slowQueryTime)) {
            $this->slowQueryTime = (float)$slowQueryTime;
        } else {
            $slowQueryTime = str_replace(',', '.', $slowQueryTime);
            if (MathUtility::canBeInterpretedAsFloat($slowQueryTime)) {
                $this->slowQueryTime = (float)$slowQueryTime;
            }
        }
    }
}

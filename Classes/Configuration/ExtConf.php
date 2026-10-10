<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Configuration;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\ApplicationType;

/**
 * This class streamlines all settings from the extension manager
 */
#[Autoconfigure(constructor: 'create')]
final readonly class ExtConf
{
    private const EXT_KEY = 'mysqlreport';

    private const DEFAULT_SETTINGS = [
        'enableFrontendLogging' => false,
        'enableBackendLogging' => false,
        'activateExplainQuery' => false,
        'slowQueryThreshold' => 10.0,
    ];

    public function __construct(
        private bool $enableFrontendLogging = self::DEFAULT_SETTINGS['enableFrontendLogging'],
        private bool $enableBackendLogging = self::DEFAULT_SETTINGS['enableBackendLogging'],
        private bool $activateExplainQuery = self::DEFAULT_SETTINGS['activateExplainQuery'],
        private float $slowQueryThreshold = self::DEFAULT_SETTINGS['slowQueryThreshold'],
    ) {}

    public static function create(ExtensionConfiguration $extensionConfiguration): self
    {
        $extensionSettings = self::DEFAULT_SETTINGS;

        // Overwrite default extension settings with values from EXT_CONF
        try {
            $extensionSettings = array_merge(
                $extensionSettings,
                $extensionConfiguration->get(self::EXT_KEY),
            );

            if (is_string($extensionSettings['slowQueryThreshold'])) {
                $extensionSettings['slowQueryThreshold'] = str_replace(
                    ',',
                    '.',
                    $extensionSettings['slowQueryThreshold'],
                );
            }
        } catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException) {
        }

        return new self(
            enableFrontendLogging: (bool)$extensionSettings['enableFrontendLogging'],
            enableBackendLogging: (bool)$extensionSettings['enableBackendLogging'],
            activateExplainQuery: (bool)$extensionSettings['activateExplainQuery'],
            slowQueryThreshold: (float)$extensionSettings['slowQueryThreshold'],
        );
    }

    public function isEnableFrontendLogging(): bool
    {
        return $this->enableFrontendLogging;
    }

    public function isEnableBackendLogging(): bool
    {
        return $this->enableBackendLogging;
    }

    public function isActivateExplainQuery(): bool
    {
        return $this->activateExplainQuery;
    }

    public function getSlowQueryThreshold(): float
    {
        return $this->slowQueryThreshold;
    }

    public function isQueryLoggingActivated(?ServerRequestInterface $request = null): bool
    {
        if (Environment::isCli()) {
            return false;
        }

        $request ??= $GLOBALS['TYPO3_REQUEST'] ?? null;
        if (!$request instanceof ServerRequestInterface) {
            return false;
        }

        $applicationType = ApplicationType::fromRequest($request);
        if ($this->enableFrontendLogging && $applicationType->isFrontend()) {
            return true;
        }

        return $this->enableBackendLogging && $applicationType->isBackend();
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Menu;

use StefanFroemken\Mysqlreport\Configuration\PanelConfiguration;
use StefanFroemken\Mysqlreport\Panel\AbstractPanel;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Model with properties for panels you can see in BE module
 */
class PageFinder
{
    /**
     * @var PackageManager
     */
    protected $packageManager;

    public function __construct(PackageManager $packageManager)
    {
        $this->packageManager = $packageManager;
    }

    public function getPageByIdentifier(string $pageIdentifier): ?Page
    {
        return $this->getAllPages()[$pageIdentifier] ?? null;
    }

    /**
     * @return \ArrayObject|Page[]
     */
    protected function getAllPages(): \ArrayObject
    {
        /** @var \ArrayObject|Page[] $pages */
        $pages = new \ArrayObject();

        foreach ($this->getPanelConfigurationFromExtensions() as $panelConfiguration) {
            if (!isset($pages[$panelConfiguration->getPageIdentifier()])) {
                $pages[$panelConfiguration->getPageIdentifier()] = GeneralUtility::makeInstance(Page::class);
            }

            $panel = $panelConfiguration->getPanel();
            if ($panel instanceof AbstractPanel) {
                $pages[$panelConfiguration->getPageIdentifier()]->attach($panel);
            }
        }

        return $pages;
    }

    /**
     * @return \SplObjectStorage|PanelConfiguration[]
     */
    protected function getPanelConfigurationFromExtensions(): \SplObjectStorage
    {
        $panelConfigurationObjects = new \SplObjectStorage();
        foreach ($this->packageManager->getActivePackages() as $activePackage) {
            $panelConfigurationFile = $activePackage->getPackagePath() . 'Configuration/MySQLPanels.php';
            if (!is_file($panelConfigurationFile)) {
                continue;
            }

            $panelsConfiguredInPackage = require $panelConfigurationFile;
            if (!is_array($panelsConfiguredInPackage)) {
                continue;
            }

            foreach ($panelsConfiguredInPackage as $panelConfiguredInPackage) {
                $panelConfiguration = $this->getPanelConfiguration($panelConfiguredInPackage);
                if ($panelConfiguration instanceof PanelConfiguration) {
                    $panelConfigurationObjects->attach($panelConfiguration);
                }
            }
        }

        return $panelConfigurationObjects;
    }

    protected function getPanelConfiguration(array $configuration): ?PanelConfiguration
    {
        /** @var PanelConfiguration $panelConfiguration */
        $panelConfiguration = GeneralUtility::makeInstance(PanelConfiguration::class, $configuration);

        return $panelConfiguration->isValid() ? $panelConfiguration : null;
    }
}

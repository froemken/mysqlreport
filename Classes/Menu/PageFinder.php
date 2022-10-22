<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Menu;

use StefanFroemken\Mysqlreport\Configuration\InfoBoxConfiguration;
use StefanFroemken\Mysqlreport\InfoBox\AbstractInfoBox;
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

        foreach ($this->getInfoBoxConfigurationFromExtensions() as $infoBoxConfiguration) {
            if (!isset($pages[$infoBoxConfiguration->getPageIdentifier()])) {
                $pages[$infoBoxConfiguration->getPageIdentifier()] = GeneralUtility::makeInstance(Page::class);
            }

            $infoBox = $infoBoxConfiguration->getInfoBox();
            if ($infoBox instanceof AbstractInfoBox) {
                $pages[$infoBoxConfiguration->getPageIdentifier()]->attach($infoBox);
            }
        }

        return $pages;
    }

    /**
     * @return \SplObjectStorage|InfoBoxConfiguration[]
     */
    protected function getInfoBoxConfigurationFromExtensions(): \SplObjectStorage
    {
        $infoBoxConfigurationObjects = new \SplObjectStorage();
        foreach ($this->packageManager->getActivePackages() as $activePackage) {
            $panelConfigurationFile = $activePackage->getPackagePath() . 'Configuration/MySqlReportInfoBoxes.php';
            if (!is_file($panelConfigurationFile)) {
                continue;
            }

            $infoBoxesConfiguredInPackage = require $panelConfigurationFile;
            if (!is_array($infoBoxesConfiguredInPackage)) {
                continue;
            }

            foreach ($infoBoxesConfiguredInPackage as $infoBoxConfiguredInPackage) {
                $infoBoxConfiguration = $this->getInfoBoxConfiguration($infoBoxConfiguredInPackage);
                if ($infoBoxConfiguration instanceof InfoBoxConfiguration) {
                    $infoBoxConfigurationObjects->attach($infoBoxConfiguration);
                }
            }
        }

        return $infoBoxConfigurationObjects;
    }

    protected function getInfoBoxConfiguration(array $configuration): ?InfoBoxConfiguration
    {
        /** @var InfoBoxConfiguration $panelConfiguration */
        $panelConfiguration = GeneralUtility::makeInstance(InfoBoxConfiguration::class, $configuration);

        return $panelConfiguration->isValid() ? $panelConfiguration : null;
    }
}

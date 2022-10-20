<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Factory;

use StefanFroemken\Mysqlreport\Menu\Page;
use StefanFroemken\Mysqlreport\Panel\AbstractPanel;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class PanelFactory
{
    /**
     * @var PackageManager
     */
    protected $packageManager;

    public function __construct(PackageManager $packageManager)
    {
        $this->packageManager = $packageManager;
    }

    public function getProcessedViewForPage(string $pageIdentifier): ?StandaloneView
    {
        return $this->getPageWithPanels()->getProcessedViewForPage($pageIdentifier);
    }

    protected function getPageWithPanels(): Page
    {
        $page = GeneralUtility::makeInstance(Page::class);
        foreach ($this->getPanelConfigurationFromExtensions() as $panelConfiguration) {
            if (
                array_key_exists('class', $panelConfiguration)
                && class_exists($panelConfiguration['class'])
            ) {
                /** @var AbstractPanel $panel */
                $panel = GeneralUtility::makeInstance($panelConfiguration['class']);
                $page->attach($panel);
            }
        }

        return $page;
    }

    protected function getPanelConfigurationFromExtensions(): array
    {
        $panelsInPackages = [];
        foreach ($this->packageManager->getActivePackages() as $activePackage) {
            $panelConfigurationFile = $activePackage->getPackagePath() . 'Configuration/MySQLPanels.php';
            if (!is_file($panelConfigurationFile)) {
                continue;
            }

            $panelsInPackage = require $panelConfigurationFile;
            if (!is_array($panelsInPackage)) {
                continue;
            }

            $panelsInPackages[] = $panelsInPackage;
        }

        return array_replace_recursive([], ...$panelsInPackages);
    }
}

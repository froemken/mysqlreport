<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Controller;

use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

/**
 * Abstract Controller with useful methods like adding buttons to BE view
 */
abstract class AbstractController extends ActionController
{
    protected ModuleTemplateFactory $moduleTemplateFactory;

    public function injectModuleTemplateFactory(ModuleTemplateFactory $moduleTemplateFactory): void
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    protected function getModuleTemplate(): ModuleTemplate
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $uriBuilder->setRequest($this->request);

        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        $buttonBar = $moduleTemplate->getDocHeaderComponent()->getButtonBar();

        // Overview
        $overviewButton = $buttonBar
            ->makeLinkButton()
            ->setShowLabelText(true)
            ->setTitle('Overview')
            ->setIcon($this->getIconFactory()->getIcon('actions-viewmode-tiles', Icon::SIZE_SMALL))
            ->setHref(
                $uriBuilder->uriFor(
                    'overview',
                    null,
                    'MySqlReport'
                )
            );
        $buttonBar->addButton($overviewButton, ButtonBar::BUTTON_POSITION_LEFT);

        // Shortcut
        if ($this->getBackendUser()->mayMakeShortcut()) {
            $shortcutButton = $buttonBar->makeShortcutButton()
                ->setRouteIdentifier('system_MysqlreportMysql')
                ->setDisplayName('Shortcut');
            $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
        }

        return $moduleTemplate;
    }

    protected function getIconFactory(): IconFactory
    {
        return GeneralUtility::makeInstance(IconFactory::class);
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
